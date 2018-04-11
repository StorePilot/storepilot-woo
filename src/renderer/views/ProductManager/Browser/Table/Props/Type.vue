<template>
  <td>
    <prop-product-type
      :prop="product.type"
      :autosave="$sp.data.autosave"
    ></prop-product-type>
  </td>
</template>

<script>
  import PropProductType from '../../../../../components/properties/PropProductType'
  export default {
    props: [
      'product'
    ],
    name: 'Type',
    components: {
      PropProductType
    }
  }
</script>
