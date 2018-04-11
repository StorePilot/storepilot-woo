<template>
  <prop-sale
    :prop="product.sale_price"
    :type="product.type.value"
    :saleFrom="product.date_on_sale_from"
    :saleTo="product.date_on_sale_to"
    :meta="product.meta_data"
    :currencies="$sp.data.currencies"
    :autosave="$sp.data.autosave"
    :cellSale="cellSale"
    :regularPrice="product.regular_price.value"
    @scheduledSale="$emit('scheduledSale')"
    @activeSale="$emit('activeSale')"
    @clear="$emit('clear')"
  ></prop-sale>
</template>

<script>
  import propSale from '../../../../../components/properties/PropSale'
  export default {
    props: [
      'product',
      'cellSale'
    ],
    name: 'Sale',
    components: {
      propSale
    }
  }
</script>
