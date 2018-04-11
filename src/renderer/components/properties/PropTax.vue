<template>
  <div style="display: inline-block">
    <el-popover
        ref="popoverTax"
        placement="left"
        trigger="click">
      <h4>{{$sp.print['Status']}}</h4>
      <el-select
          v-model="taxStatus.value"
          v-loading="taxStatus.loading"
          @change="autosave ? taxStatus.save() : ''"
          :placeholder="$sp.print['TaxStatus']">
        <el-option
            v-for="(t, index) in tax_states"
            :key="index"
            :label="t.label"
            :value="t.value">
        </el-option>
      </el-select>
      <h4>{{$sp.print['Class']}}</h4>
      <el-select
          v-model="taxClass.value"
          v-loading="taxClass.loading"
          @change="autosave ? taxClass.save() : ''"
          :placeholder="$sp.print['TaxClass']">
        <!--el-option @todo - tobe supported in wc 3.1.1 @see https://github.com/woocommerce/woocommerce/issues/15960
          label="None"
          value=""></el-option-->
        <el-option
            v-for="(opt, index) in taxOptions"
            :key="index"
            :label="opt.name.value"
            :value="opt.slug.value">
        </el-option>
      </el-select>
    </el-popover>
    <el-button
        v-popover:popoverTax
        style="margin-left: 10px;"
        type="text"
        size="mini">
      {{$sp.print['Tax']}}
    </el-button>
  </div>
</template>

<script>
  export default {
    props: [
      'taxStatus',
      'taxClass',
      'taxOptions',
      'autosave'
    ],
    name: 'prop-tax',
    data () {
      return {
        tax_states: [
          {
            label: 'Taxable',
            value: 'taxable'
          },
          {
            label: 'Shipping',
            value: 'shipping'
          },
          {
            label: 'None',
            value: 'none'
          }
        ]
      }
    }
  }
</script>
