<template>
  <prop-sale
    :prop="variation.sale_price"
    :type="'simple'"
    :saleFrom="variation.date_on_sale_from"
    :saleTo="variation.date_on_sale_to"
    :meta="variation.meta_data"
    :currencies="$sp.data.currencies"
    :autosave="$sp.data.autosave"
    :cellSale="cellSale"
    :regularPrice="variation.regular_price.value"
    @scheduledSale="$emit('scheduledSale')"
    @activeSale="$emit('activeSale')"
    @clear="$emit('clear')"
  ></prop-sale>
</template>

<script>
  import propSale from '../../../../../components/properties/PropSale'
  export default {
    props: [
      'variation',
      'cellSale'
    ],
    name: 'Sale',
    components: {
      propSale
    }
  }
</script>
