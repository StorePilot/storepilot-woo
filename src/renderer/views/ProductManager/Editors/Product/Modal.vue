<template>
  <!-- MAIN INSTANCE OF PRODUCT -->
  <el-dialog
    :title="$sp.print['Product']"
    fullscreen
    width="90%"
    class="sp-modal sp-modal-top"
    :visible.sync="data.modalVisible">
    <product :data="data" :browser="browser" :product="product"></product>
  </el-dialog>
</template>

<script>
  import product from './Product'
  export default {
    name: 'Product',
    components: {
      product
    },
    props: [
      'data',
      'browser',
      'product'
    ]
  }
</script>
