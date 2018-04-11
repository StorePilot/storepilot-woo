<template>
  <div>
    <el-tag
        :key="index"
        style="margin: 0 10px 10px 0;"
        v-for="(tag, index) in prop.value"
        v-loading="prop.loading"
        :closable="true"
        :close-transition="true"
        @close="handleTagClose(tag)">
      {{tag.name}}
    </el-tag>
    <el-autocomplete
        v-show="tagInput"
        v-model="tagInputString"
        ref="tagInputString"
        class="inline-input"
        :fetch-suggestions="tagSearch"
        :placeholder="$sp.print['PleaseInput']"
        size="small"
        @keyup.enter.native="handleTagCreate"
        @select="handleTagCreate">
      <el-button
          @click="handleTagCreate"
          slot="append"
          icon="el-icon-circle-check"></el-button>
    </el-autocomplete>
    <el-button
        v-show="!tagInput"
        :class="'button-new-tag'"
        size="small"
        @click="showTagInput">
      + {{$sp.print['Tag']}}
    </el-button>
  </div>
</template>

<script>
  export default {
    props: [
      'prop',
      'tags',
      'autosave'
    ],
    name: 'prop-tags',
    data () {
      return {
        tagInput: false,
        tagInputString: ''
      }
    },
    methods: {
      showTagInput () {
        this.tagInput = true
        this.tagInputString = ''
        this.$nextTick(_ => {
          this.$refs.tagInputString.$refs.input.$refs.input.focus()
        })
      },
      tagSearch (query, callback) {
        let tags = []
        this.tags.forEach(tag => {
          if (typeof query !== 'undefined' && tag.name.value.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
            tags.push({
              value: tag.name.value,
              label: tag.name.value
            })
          }
        })
        callback(tags)
      },
      handleTagCreate () {
        let scope = this
        let tag = null
        this.tags.forEach(t => {
          if (t.name.value === this.tagInputString) {
            tag = t
          }
        })
        if (this.prop.value === 0) {
          this.prop.value = []
        }
        if (tag !== null) {
          this.prop.value.push({id: tag.id.value, name: tag.name.value})
          if (this.autosave) { this.prop.save() }
        } else if (this.tagInputString.length > 0) {
          let tag = new scope.$sp.models.ProductTag(scope.$pap.controller)
          tag.name.value = this.tagInputString
          tag.save().then(() => {
            /* scope.$message({
              message: 'Tag Created',
              type: 'success'
            }) */
            scope.tags.push(tag)
            scope.prop.value.push({
              id: tag.id.value,
              name: tag.name.value
            })
            if (scope.autosave) { scope.prop.save() }
          })
        }
        this.tagInput = false
      },
      handleTagClose (tag) {
        let index = this.prop.value.indexOf(tag)
        this.prop.value.splice(index, 1)
        if (this.prop.value.length === 0) {
          this.prop.value = 0
        } else if (typeof this.prop.value[0].position !== 'undefined') {
          this.prop.value[0].position = 0
        }
        if (this.autosave) {
          this.prop.save()
        }
      }
    }
  }
</script>
