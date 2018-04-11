<template>
  <td>
    <el-popover
      @show="popover=true"
      @hide="popover=false"
      ref="popoverCats"
      placement="left"
      trigger="click">
      <prop-categories
        v-if="popover"
        :prop="product.categories"
        :categoriesNested="$sp.data.categoriesNested"
        :autosave="$sp.data.autosave"
      ></prop-categories>
    </el-popover>
    <el-button
      v-popover:popoverCats
      style="margin-left: 10px;"
      type="text">
      {{$sp.print['Edit']}}
    </el-button>
    ({{product.categories.value.length}})
  </td>
</template>

<script>
  import PropCategories from '../../../../../components/properties/PropCategories'
  export default {
    props: [
      'product'
    ],
    data () {
      return {
        popover: false
      }
    },
    components: {
      PropCategories
    },
    name: 'Categories'
  }
</script>
