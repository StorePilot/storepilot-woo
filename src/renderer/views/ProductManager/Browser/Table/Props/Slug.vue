<template>
  <td style="min-width: 200px!important">
    <el-input
      v-loading="product.slug.loading"
      @blur="$sp.data.autosave ? product.slug.save() : ''"
      v-model="product.slug.value"
      placeholder="$sp.print['Slug']"></el-input>
  </td>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Slug'
  }
</script>
