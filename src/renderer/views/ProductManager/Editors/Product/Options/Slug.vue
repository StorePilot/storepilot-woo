<template>
  <tr>
    <td>{{$sp.print['Slug']}}</td>
    <td>
      <el-input
        :placeholder="$sp.print['Slug']"
        v-model="product.slug.value"
        v-loading="product.slug.loading"
        @blur="$sp.data.autosave ? product.slug.save() : ''">
      </el-input>
    </td>
  </tr>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Slug'
  }
</script>
