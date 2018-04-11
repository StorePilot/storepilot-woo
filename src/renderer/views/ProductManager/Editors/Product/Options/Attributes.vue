<template>
  <tr>
    <td>{{$sp.print['Attributes']}}</td>
    <td>
      <prop-product-attributes
        :autosave="$sp.data.autosave"
        :prop="product.attributes"
        :attributes="$sp.data.productAttributes"
        :terms="$sp.data.productAttributeTerms"
        :defaultAttr="product.default_attributes"
        :product="product"
      ></prop-product-attributes>
    </td>
  </tr>
</template>

<script>
  import propProductAttributes from '../../../../../components/properties/PropProductAttributes/PropProductAttributes'
  export default {
    props: [
      'product'
    ],
    name: 'Attributes',
    components: {
      propProductAttributes
    }
  }
</script>
