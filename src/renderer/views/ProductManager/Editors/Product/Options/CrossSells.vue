<template>
  <tr>
    <td>{{$sp.print['CrossSells']}}</td>
    <td>
      <prop-products
        @open="open"
        :parent="product.id.value"
        :prop="product.cross_sell_ids"
        :products="product.cross_sell"
        :autosave="$sp.data.autosave"
        :currencies="$sp.data.currencies"
      ></prop-products>
    </td>
  </tr>
</template>

<script>
  import propProducts from '../../../../../components/properties/PropProducts'
  export default {
    props: [
      'data',
      'product'
    ],
    name: 'CrossSells',
    components: {
      propProducts
    },
    methods: {
      open (prod) {
        this.data.modal = prod
        this.data.modalVisible = true
      }
    }
  }
</script>
