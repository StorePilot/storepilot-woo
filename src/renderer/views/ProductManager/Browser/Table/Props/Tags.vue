<template>
  <td>
    <el-popover
      @show="popover=true"
      @hide="popover=false"
      ref="popoverTags"
      placement="left"
      trigger="click">
      <prop-tags
        v-if="popover"
        :prop="product.tags"
        :tags="$sp.data.tags.children"
        :autosave="$sp.data.autosave"
      ></prop-tags>
    </el-popover>
    <el-button
      v-popover:popoverTags
      style="margin-left: 10px;"
      type="text">
      {{$sp.print['Edit']}}
    </el-button>
    ({{product.tags.value.length}})
  </td>
</template>

<script>
  import PropTags from '../../../../../components/properties/PropTags'
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
      PropTags
    },
    name: 'Tags'
  }
</script>
