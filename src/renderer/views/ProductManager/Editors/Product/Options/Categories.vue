<template>
  <tr>
    <td>{{$sp.print['Categories']}}</td>
    <td class="sp-product-categories">
      <prop-categories
        :prop="product.categories"
        :categoriesNested="$sp.data.categoriesNested"
        :autosave="$sp.data.autosave"
      ></prop-categories>
    </td>
  </tr>
</template>

<script>
  import PropCategories from '../../../../../components/properties/PropCategories'
  export default {
    props: [
      'product'
    ],
    name: 'Categories',
    components: {
      PropCategories
    }
  }
</script>
