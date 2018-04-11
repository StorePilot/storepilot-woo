<template>
  <td>
    {{product.variations.value.length}}
  </td>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Variations'
  }
</script>
