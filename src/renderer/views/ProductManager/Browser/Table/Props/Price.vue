<template>
  <td>
    <el-input
      v-model="product.regular_price.value"
      :placeholder="$sp.print['Price']"></el-input>
  </td>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Price'
  }
</script>
