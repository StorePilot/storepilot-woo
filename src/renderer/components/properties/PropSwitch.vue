<template>
  <el-switch
      v-loading="prop.loading"
      @change="autosave ? prop.save() : ''"
      v-model="prop.value"
      active-color="#13ce66"
      active-text=""
      inactive-text="">
  </el-switch>
</template>

<script>
  export default {
    props: [
      'prop',
      'autosave'
    ],
    name: 'prop-switch'
  }
</script>
