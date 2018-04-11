<template>
  <td style="min-width: 200px!important">
    <div class="sp-table-products">
      <prop-products
        @open="open"
        :parent="product.id.value"
        :prop="product.cross_sell_ids"
        :products="product.cross_sell"
        :currencies="$sp.data.currencies"
        :autosave="$sp.data.autosave"
        :size="'small'"
      ></prop-products>
    </div>
  </td>
</template>

<script>
  import propProducts from '../../../../../components/properties/PropProducts'
  export default {
    props: [
      'product'
    ],
    components: {
      propProducts
    },
    name: 'CrossSells',
    methods: {
      open (prod) {
        this.data.productClickedId = prod.id.value
        this.data.product = prod
        this.$router.push({
          name: 'Product',
          params: {
            id: prod.id.value
          }
        })
      }
    }
  }
</script>

<style>
  .sp-table-products {
    height: 40px;
    width: 100%;
    overflow-y: scroll;
  }
</style>
