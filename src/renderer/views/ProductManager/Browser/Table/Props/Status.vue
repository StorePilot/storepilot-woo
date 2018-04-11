<template>
  <td>
    <prop-product-status
      :prop="product.status"
      :autosave="$sp.data.autosave"
    ></prop-product-status>
  </td>
</template>

<script>
  import PropProductStatus from '../../../../../components/properties/PropProductStatus'
  export default {
    props: [
      'product'
    ],
    name: 'Status',
    components: {
      PropProductStatus
    }
  }
</script>
