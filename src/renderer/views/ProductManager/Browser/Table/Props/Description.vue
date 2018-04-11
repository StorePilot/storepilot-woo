<template>
  <td style="white-space: nowrap">
    <el-popover
      @show="popover=true"
      @hide="popover=false"
      ref="popoverDescription"
      placement="left"
      trigger="click">
      <prop-textarea
        v-if="popover"
        :raw="product.description_raw"
        :prop="product.description"
        :autosave="$sp.data.autosave"
      ></prop-textarea>
    </el-popover>
    <el-button
      v-popover:popoverDescription
      style="margin-left: 10px;"
      type="text">
      {{$sp.print['Edit']}}
    </el-button>
    ({{product.description_raw.value.length}})
  </td>
</template>

<script>
  import propTextarea from '../../../../../components/properties/PropTextarea'
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
      propTextarea
    },
    name: 'Description'
  }
</script>
