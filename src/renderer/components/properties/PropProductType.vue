<template>
  <el-select
      v-loading="prop.loading"
      v-model="prop.value"
      @change="autosave ? prop.save() : ''"
      :placeholder="$sp.print['Type']">
    <el-option
        v-for="(t, index) in types"
        :key="index"
        :label="t.label"
        :disabled="t.disabled"
        :value="t.value">
    </el-option>
  </el-select>
</template>

<script>
  export default {
    props: [
      'prop',
      'autosave'
    ],
    name: 'prop-product-type',
    data () {
      return {
        types: [
          {
            label: 'Simple Product',
            value: 'simple'
          },
          {
            label: 'Grouped Product',
            value: 'grouped'
          },
          {
            label: 'External Product',
            value: 'external'
          },
          {
            label: 'Variable Product',
            value: 'variable'
          }
        ]
      }
    }
  }
</script>
