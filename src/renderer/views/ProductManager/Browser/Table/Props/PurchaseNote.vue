<template>
  <td style="white-space: nowrap">
    <el-popover
      @show="popover=true"
      @hide="popover=false"
      ref="popoverPurchaseNote"
      placement="left"
      trigger="click">
      <prop-textarea
        v-if="popover"
        :raw="product.purchase_note_raw"
        :prop="product.purchase_note"
        :autosave="$sp.data.autosave"
      ></prop-textarea>
    </el-popover>
    <el-button
      v-popover:popoverPurchaseNote
      style="margin-left: 10px;"
      type="text">
      {{$sp.print['Edit']}}
    </el-button>
    ({{product.purchase_note_raw.value.length}})
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
    name: 'PurchaseNote'
  }
</script>
