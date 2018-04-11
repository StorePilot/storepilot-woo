<template>
  <div>
    <el-input
        v-if="type === 'simple' || type === 'external'"
        v-loading="prop.loading"
        style="width: 45%"
        :style="{
          'text-decoration': activeSale ? 'line-through' : 'none'
        }"
        type="text"
        placeholder="0"
        @blur="autosave ? prop.save() : ''"
        v-model="prop.value">
      <template slot="append" v-if="typeof currencies.value !== 'undefined'">
        {{ currencies !== null ? $sp.valuta.symbol(currencies.value.value) : '?' }}
      </template>
    </el-input>
    <el-input
        v-else
        v-loading="prop.loading"
        style="width: 45%"
        :style="{
          'text-decoration': (activeSale && priceRange.value === null) ? 'line-through' : 'none'
        }"
        type="text"
        placeholder="0"
        disabled
        :value="priceRange.value !== null ? (priceRange.value.min + ' - ' + priceRange.value.max) : '0 - 0'">
      <template slot="append" v-if="typeof currencies.value !== 'undefined'">
        {{ currencies !== null ? $sp.valuta.symbol(currencies.value.value) : '?' }}
      </template>
    </el-input>
    <el-button
        v-if="type === 'simple' || type === 'external'"
        style="margin-left: 5px;"
        type="text"
        size="small"
        @click="toggleSale()">
      {{$sp.print['EditSale']}}
      <span
          style="color: #777;"
          v-if="!activeSale && !scheduledSale">
        - {{$sp.print['SaleInactive']}}
      </span>
      <span
          style="color: #777;"
          v-if="activeSale">
        - {{$sp.print['SaleActive']}} <i class="el-icon-warning"></i>
      </span>
      <span
          style="color: #777;"
          v-if="!activeSale && scheduledSale">
        - {{$sp.print['SaleScheduled']}} <i class="el-icon-time"></i>
      </span>
    </el-button>
  </div>
</template>

<script>
  export default {
    props: [
      'prop',
      'priceRange',
      'type',
      'currencies',
      'scheduledSale',
      'activeSale',
      'autosave'
    ],
    name: 'prop-price',
    methods: {
      toggleSale () {
        this.$emit('toggleSale')
      }
    }
  }
</script>
