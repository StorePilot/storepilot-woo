<template>
  <td style="min-width: 300px!important">
    <el-input
      v-loading="product.name.loading"
      @blur="$sp.data.autosave ? product.name.save() : ''"
      v-model="product.name.value"
      :placeholder="$sp.print['Name']"></el-input>
  </td>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Name'
  }
</script>
