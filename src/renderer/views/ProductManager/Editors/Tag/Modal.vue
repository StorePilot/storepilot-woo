<template>
  <el-dialog
    fullscreen
    width="90%"
    class="sp-modal"
    :title="$sp.print['Tag']"
    :visible.sync="data.modalTagVisible">
    <sp-tag :data="data" :tag="tag"></sp-tag>
  </el-dialog>
</template>

<script>
  import SpTag from './Tag'
  export default {
    props: [
      'data',
      'tag'
    ],
    components: {
      SpTag
    }
  }
</script>
