<template>
  <tr>
    <td>{{$sp.print['Status']}}</td>
    <td>
      <prop-product-status
        :prop="product.status"
        :autosave="$sp.data.autosave">
      </prop-product-status>
    </td>
  </tr>
</template>

<script>
  import propProductStatus from '../../../../../components/properties/PropProductStatus'
  export default {
    props: [
      'product'
    ],
    name: 'Status',
    components: {
      propProductStatus
    }
  }
</script>
