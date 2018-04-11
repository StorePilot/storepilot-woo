<template>
  <tr>
    <td>{{$sp.print['MetaData']}}</td>
    <td>
      <prop-meta
        :prop="product.meta_data"
        :product="product">
      </prop-meta>
    </td>
  </tr>
</template>

<script>
  import propMeta from '../../../../../components/properties/PropMeta'
  export default {
    props: [
      'product'
    ],
    name: 'Meta',
    components: {
      propMeta
    }
  }
</script>
