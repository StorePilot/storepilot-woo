<template>
  <tr>
    <td>
      {{$sp.print['Price']}}
      <prop-tax
        :autosave="$sp.data.autosave"
        :taxStatus="variation.tax_status"
        :taxClass="variation.tax_class"
        :taxOptions="$sp.data.shippingClasses.children"
      ></prop-tax>
    </td>
    <td>
      <prop-price
        :prop="variation.regular_price"
        :currencies="$sp.data.currencies"
        type="simple"
        :scheduledSale="saleIsScheduled"
        :activeSale="saleIsActive"
        :autosave="$sp.data.autosave"
        @toggleSale="$emit('toggleSale')"
      ></prop-price>
    </td>
  </tr>
</template>

<script>
  import propPrice from '../../../../../components/properties/PropPrice'
  import propTax from '../../../../../components/properties/PropTax'
  export default {
    props: [
      'variation',
      'saleIsScheduled',
      'saleIsActive'
    ],
    name: 'Price',
    components: {
      propPrice,
      propTax
    }
  }
</script>
