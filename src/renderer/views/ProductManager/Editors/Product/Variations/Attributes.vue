<template>
  <tr>
    <td>{{$sp.print['Attributes']}}</td>
    <td>
      <prop-product-attributes
        :autosave="$sp.data.autosave"
        :prop="product.attributes"
        :attributes="$sp.data.productAttributes"
        :terms="$sp.data.productAttributeTerms"
        :variableAttr="variation.attributes"
        :product="product"
      ></prop-product-attributes>
    </td>
  </tr>
</template>

<script>
  import propProductAttributes from '../../../../../components/properties/PropProductAttributes/PropProductAttributes'
  export default {
    props: [
      'variation',
      'product'
    ],
    name: 'Attributes',
    components: {
      propProductAttributes
    }
  }
</script>
