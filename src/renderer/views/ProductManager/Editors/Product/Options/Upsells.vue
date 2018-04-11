<template>
  <tr>
    <td>{{$sp.print['Upsells']}}</td>
    <td style="min-height: 20px;">
      <prop-products
        @open="open"
        :parent="product.id.value"
        :prop="product.upsell_ids"
        :products="product.upsell"
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
    name: 'Upsells',
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
