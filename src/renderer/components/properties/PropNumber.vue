<template>
  <el-input-number
      :placeholder="placeholder"
      v-loading="prop.loading"
      v-model="prop.value">
  </el-input-number>
</template>

<script>
  export default {
    props: [
      'prop',
      'autosave',
      'placeholder'
    ],
    name: 'prop-number',
    watch: {
      'prop.value' () {
        if (this.autosave) {
          this.prop.save()
        }
      }
    }
  }
</script>
