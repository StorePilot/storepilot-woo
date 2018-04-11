<template>
  <td>
    <el-input
      v-model="product.sale_price.value"
      :placeholder="$sp.print['Sale']"></el-input>
  </td>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Sale'
  }
</script>
