<template>
  <el-dialog
    fullscreen
    width="90%"
    class="sp-modal"
    :title="$sp.print['Settings']"
    :visible.sync="data.modalSettingsVisible">
      <settings :data="data"></settings>
  </el-dialog>
</template>

<script>
  import settings from './Settings'
  export default {
    name: 'Settings',
    props: [
      'data'
    ],
    components: {
      settings
    }
  }
</script>
