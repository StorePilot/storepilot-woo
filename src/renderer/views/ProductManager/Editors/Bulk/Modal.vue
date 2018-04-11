<template>
  <el-dialog
    fullscreen
    width="90%"
    class="sp-modal"
    :title="$sp.print['BulkEdit']"
    :visible.sync="data.modalBulkVisible">
      <sp-products :data="data" :browser="browser"></sp-products>
  </el-dialog>
</template>

<script>
  import spProducts from './Products'
  export default {
    name: 'products',
    props: [
      'data',
      'browser'
    ],
    components: {
      spProducts
    }
  }
</script>
