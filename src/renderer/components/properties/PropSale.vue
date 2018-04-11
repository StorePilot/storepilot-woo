<template>
  <tbody
      v-if="type === 'simple' || type === 'external'"
      v-show="cellSale || sale"
      class="prop-sale">
    <tr>
      <td style="padding-left: 20px;">
        {{$sp.print['SalePrice']}}
      </td>
      <td>
        <el-input
            v-if="typeof currencies.value !== 'undefined'"
            v-loading="prop.loading"
            style="width: 92%"
            :style="{
              'text-decoration': !sale ? 'line-through' : 'none'
            }"
            type="text"
            placeholder="0"
            @blur="autosave ? prop.save() : ''"
            v-model="prop.value">
          <template slot="append">
            {{currencies !== null ? $sp.valuta.symbol(currencies.value.value) : '?'}}
          </template>
        </el-input>
      </td>
    </tr>
    <tr>
      <td style="padding-left: 20px;">
        {{$sp.print['Schedule']}}
      </td>
      <td>
        <el-date-picker
            style="width: 45%"
            v-model="saleFrom.value"
            v-loading="saleFrom.loading"
            type="date"
            @change="autosave ? saleFrom.save() : ''"
            :placeholder="$sp.print['From']">
        </el-date-picker>
        <el-date-picker
            style="width: 45%"
            v-model="saleTo.value"
            v-loading="saleTo.loading"
            type="date"
            @change="autosave ? saleTo.save() : ''"
            :placeholder="$sp.print['To']">
        </el-date-picker>
      </td>
    </tr>
    <tr>
      <td style="padding-left: 20px;">
        {{$sp.print['SaleFlash']}}
      </td>
      <td>
        <el-input
            v-loading="meta.loading"
            style="width: 92%"
            type="text"
            :placeholder="$sp.print['Sale!']"
            @blur="autosave ? meta.save().then(function () { meta.fetch() }) : ''"
            v-model="saleLabel.value">
          <template slot="append">
            {{$sp.print['Label']}}
          </template>
        </el-input>
      </td>
    </tr>
    <tr style="border-bottom: 20px solid rgb(238, 241, 246)">
      <td></td>
      <td>
        <el-button @click="clearSale()">
          {{$sp.print['ClearSale']}}
        </el-button>
      </td>
    </tr>
    <div class="border"></div>
  </tbody>
</template>

<script>
  import moment from 'moment'
  export default {
    props: [
      'prop',
      'priceRange',
      'saleFrom',
      'saleTo',
      'type',
      'meta',
      'currencies',
      'scheduledSale',
      'cellSale',
      'autosave',
      'regularPrice'
    ],
    name: 'prop-sale',
    data () {
      return {
        sale: false,
        sched: false,
        saleFromCopyValue: '',
        saleToCopyValue: '',
        propCopyValue: ''
      }
    },
    mounted () {
      this.scheduled()
    },
    watch: {
      saleFromCopyValue (val) {
        if (typeof val !== 'undefined' && val !== null && val !== '') {
          if (val.length === 19) {
            // If just received from request, transform date
            this.saleFromCopyValue = val + '.000Z'
            this.saleFrom.value = this.saleFromCopyValue
            this.saleFrom.changed(false) // keep unchanged
          } else if (String(val).indexOf('T00:00:00.000Z') === -1) {
            this.saleFromCopyValue = moment(val).format('YYYY-MM-DDT00:00:00.000[Z]')
            this.saleFrom.value = this.saleFromCopyValue
          }
        } else {
          this.saleFromCopyValue = ''
          this.saleFrom.value = null
        }
        this.scheduled()
      },
      saleToCopyValue (val) {
        if (typeof val !== 'undefined' && val !== null && val !== '') {
          if (val.length === 19) {
            // If just received from request, transform date
            this.saleToCopyValue = val + '.000Z'
            this.saleTo.value = this.saleToCopyValue
            this.saleTo.changed(false) // keep unchanged
          } else if (String(val).indexOf('T00:00:00.000Z') === -1) {
            this.saleToCopyValue = moment(val).format('YYYY-MM-DDT00:00:00.000[Z]')
            this.saleTo.value = this.saleToCopyValue
          }
        } else {
          this.saleToCopyValue = ''
          this.saleTo.value = null
        }
        this.scheduled()
      },
      propCopyValue (val) {
        if (typeof val !== 'number') {
          this.prop.value = null
        } else {
          this.prop.value = val
        }
      },
      'saleFrom.value' (val) {
        if (val !== this.saleFromCopyValue) {
          this.saleFromCopyValue = val
        }
      },
      'saleTo.value' (val) {
        if (val !== this.saleToCopyValue) {
          this.saleToCopyValue = val
        }
      },
      'prop.value' (val) {
        if (typeof val === 'number') {
          this.propCopyValue = val
        } else {
          this.propCopyValue = ''
        }
        this.scheduled()
      },
      'regularPrice' () {
        this.scheduled()
      }
    },
    methods: {
      clearSale () {
        this.saleFrom.value = null
        this.saleFrom.changed(true)
        this.saleTo.value = null
        this.saleTo.changed(true)
        this.prop.value = null
        this.saleLabel.value = ''
        if (this.autosave) {
          this.prop.save()
        }
        this.$emit('clear')
      },
      scheduled () {
        if (
          (
            moment().isBefore(moment.parseZone(this.saleFrom.value)) &&
            moment.parseZone(this.saleTo.value).isAfter(moment.parseZone(this.saleFrom.value))
          ) ||
          (
            moment().isBefore(moment.parseZone(this.saleFrom.value)) &&
            (this.saleTo.value === '' || this.saleTo.value === null)
          )
        ) {
          this.sched = true
        } else {
          this.sched = false
        }
        if (
          (
            (this.saleFrom.value === '' || this.saleFrom.value === null) &&
            moment().isBefore(moment.parseZone(this.saleTo.value))
          ) ||
          (
            moment().isAfter(moment.parseZone(this.saleFrom.value)) &&
            moment().isBefore(moment.parseZone(this.saleTo.value))
          ) ||
          (
            moment().isAfter(moment.parseZone(this.saleFrom.value)) &&
            (this.saleTo.value === '' || this.saleTo.value === null)
          ) ||
          (
            (this.saleFrom.value === '' || this.saleFrom.value === null) &&
            (this.saleTo.value === '' || this.saleTo.value === null)
          )
        ) {
          this.sale = true
        } else {
          this.sale = false
        }
        if (
          this.prop.value === null ||
          this.prop.value === '' ||
          Number(this.prop.value) >= Number(this.regularPrice)
        ) {
          this.sched = false
          this.sale = false
        }
        this.$emit('scheduledSale', this.sched)
        this.$emit('activeSale', this.sale)
      }
    },
    computed: {
      saleLabel () {
        let key = 'sp_custom_sale_flash_label'
        if (this.meta.value === null || this.meta.value.constructor !== Array) {
          this.meta.value = []
        }
        let label = this.meta.value.find(m => m.key === key)
        if (typeof label === 'undefined') {
          label = { key: key, value: '' }
          this.meta.value.push(label)
          this.meta.changed(false)
        }
        return label
      }
    }
  }
</script>

<style>
  .prop-sale {
    background-color: #fff;
    width: 100%;
    border-top: 20px solid white;
  }
  .prop-sale td {
    padding-bottom: 25px;
    margin-bottom: 20px;
  }
  .prop-sale td:first-child {
    width: 35%;
  }
  .prop-sale td:last-child {
    width: 65%;
  }
  .border {
    display: table-row;
    height: 20px;
    border-bottom: 20px solid transparent;
  }
</style>
