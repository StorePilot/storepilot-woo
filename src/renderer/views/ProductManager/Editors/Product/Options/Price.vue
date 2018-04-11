<template>
  <tr>
    <td>
      {{$sp.print['RegularPrice']}}
      <prop-tax
        v-if="product.type.value !== 'grouped'"
        :autosave="$sp.data.autosave"
        :taxStatus="product.tax_status"
        :taxClass="product.tax_class"
        :taxOptions="$sp.data.shippingClasses.children"
      ></prop-tax>
    </td>
    <td>
      <prop-price
        :prop="product.regular_price"
        :priceRange="product.price_range"
        :currencies="$sp.data.currencies"
        :type="product.type.value"
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
      'product',
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
