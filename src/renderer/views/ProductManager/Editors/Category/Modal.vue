<template>
  <el-dialog
    fullscreen
    width="90%"
    class="sp-modal"
    :title="$sp.print['Category']"
    :visible.sync="data.modalCatVisible">
    <category :data="data" :category="category"></category>
  </el-dialog>
</template>

<script>
  import category from './Category'
  export default {
    props: [
      'data',
      'category'
    ],
    components: {
      category
    }
  }
</script>
