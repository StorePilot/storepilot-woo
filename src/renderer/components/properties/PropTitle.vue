<template>
  <el-input
      :placeholder="placeholder"
      size="small"
      v-model="prop.value"
      class="sp-editor-title-input"
      v-loading="prop.loading"
      @blur="autosave ? prop.save() : ''">
  </el-input>
</template>

<script>
  export default {
    props: [
      'prop',
      'autosave',
      'placeholder'
    ],
    name: 'prop-title'
  }
</script>

<style>
  .sp-editor-title-input input {
    background: transparent!important;
    background-image: none;
    border: none;
    padding: 0;
    margin: 10px 0 7px;
    font-weight: bold;
    text-overflow: ellipsis;
    font-size: 1.5em;
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
  }
  .sp-editor-title-input input:focus {
    text-decoration: underline;
    text-decoration-style: dashed;
    text-decoration-color: rgb(29, 140, 224);
  }
</style>
