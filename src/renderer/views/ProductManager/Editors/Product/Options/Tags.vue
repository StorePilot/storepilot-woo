<template>
  <tr>
    <td>{{$sp.print['Tags']}}</td>
    <td class="sp-product-tags">
      <prop-tags
        :prop="product.tags"
        :tags="$sp.data.tags.children"
        :autosave="$sp.data.autosave"
      ></prop-tags>
    </td>
  </tr>
</template>

<script>
  import PropTags from '../../../../../components/properties/PropTags'
  export default {
    props: [
      'product'
    ],
    name: 'Tags',
    components: {
      PropTags
    }
  }
</script>
