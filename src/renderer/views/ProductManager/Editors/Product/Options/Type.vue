<template>
  <tr>
    <td>{{$sp.print['Type']}}</td>
    <td>
      <prop-product-type
        :prop="product.type"
        :autosave="$sp.data.autosave">
      </prop-product-type>
      <el-button
        v-if="product.type.value === 'variable'"
        @click="$emit('goToVariations')"
        type="text"
        size="mini">
        Edit Variations
      </el-button>
    </td>
  </tr>
</template>

<script>
  import propProductType from '../../../../../components/properties/PropProductType'
  export default {
    props: [
      'product'
    ],
    name: 'Type',
    components: {
      propProductType
    }
  }
</script>
