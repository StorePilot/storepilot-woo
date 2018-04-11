<template>
  <el-select
      v-loading="prop.loading"
      v-model="prop.value"
      @change="autosave ? prop.save() : ''"
      :placeholder="$sp.print['Status']">
    <el-option
        v-for="(t, index) in states"
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
    name: 'prop-product-status',
    data () {
      return {
        states: [
          {
            label: 'Published',
            value: 'publish'
          },
          {
            label: 'Draft',
            value: 'draft'
          },
          {
            label: 'Pending',
            value: 'pending'
          },
          {
            label: 'Private',
            value: 'private'
          },
          {
            label: 'Trash',
            value: 'trash',
            disabled: true
          }
        ]
      }
    }
  }
</script>
