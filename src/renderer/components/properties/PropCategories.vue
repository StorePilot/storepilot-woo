<template>
  <div v-loading="prop.loading">
    <el-input
        class="sp-cat-filter"
        :placeholder="$sp.print['FilterCategories']"
        v-model="filterText">
    </el-input>
    <el-tree
        class="sp-cat-tree"
        ref="categoryTree"
        node-key="id"
        :check-strictly="true"
        :data="tree"
        :default-expanded-keys="categoryIds"
        :default-checked-keys="categoryIds"
        :filter-node-method="filterNode"
        @check-change="handleCheckChange"
        show-checkbox>
    </el-tree>
  </div>
</template>

<script>
  export default {
    props: [
      'prop',
      'categoriesNested',
      'autosave'
    ],
    name: 'prop-categories',
    computed: {
      categoryIds () {
        let ids = []
        if (typeof this.prop !== 'undefined' && this.prop.value !== null && this.prop.value.constructor === Array) {
          this.prop.value.forEach(category => {
            ids.push(category.id)
          })
        }
        return ids
      },
      tree () {
        let tree = []
        this.categoriesNested.forEach(cat => {
          tree.push(this.makeBranch(cat))
        })
        return tree
      }
    },
    data () {
      return {
        filterText: ''
      }
    },
    mounted () {
      this.init()
    },
    watch: {
      categoryIds (ids) {
        this.init()
      },
      filterText (val) {
        this.$refs.categoryTree.filter(val)
      }
    },
    methods: {
      makeBranch (child) {
        let branch = {
          id: child.id.value,
          label: child.name.value,
          children: []
        }
        child.children.forEach(c => {
          branch.children.push(this.makeBranch(c))
        })
        return branch
      },
      filterNode (value, data) {
        if (!value) {
          return true
        } else {
          return data.label.toLowerCase().indexOf(value.toLowerCase()) !== -1
        }
      },
      init () {
        if (this.categoryIds.constructor === Array) {
          if (typeof this.$refs.categoryTree !== 'undefined') {
            this.$refs.categoryTree.setCheckedKeys(this.categoryIds)
          } else {
            let scope = this
            // Wait for scope.$refs.categoryTree to be mounted. @todo -> @see https://github.com/vuejs/vue/issues/3842
            setTimeout(() => {
              scope.$refs.categoryTree.setCheckedKeys(scope.categoryIds)
            }, 500)
          }
        }
      },
      handleCheckChange (data, checked) {
        let inCurrent = this.categoryIds.indexOf(data.id) !== -1
        if ((inCurrent && !checked) || (!inCurrent && checked)) {
          let categories = []
          this.prop.value.forEach(category => {
            if (data.id !== category.id) {
              categories.push({ id: category.id })
            }
          })
          if (checked) {
            categories.push({ id: data.id })
          }
          this.prop.value = categories
          if (this.autosave) { this.prop.save() }
        }
      }
    }
  }
</script>

<style>
  .sp-cat-filter {
    margin-bottom: 20px;
  }
  .el-tree {
    resize: vertical;
    height: 150px;
    overflow-y: scroll;
  }
  .sp-cat-tree {
    background: none!important;
  }
</style>
