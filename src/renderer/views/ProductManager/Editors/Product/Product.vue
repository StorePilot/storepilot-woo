<template>
  <!-- MAIN INSTANCE OF PRODUCT -->
  <div
      v-if="data[product] !== null && typeof data[product] !== 'undefined'"
      v-loading="data[product].loading || loading"
      class="sp-editor-wrapper">

    <!-- HEADER -->
    <product-warnings :product="data[product]"></product-warnings>
    <product-header :product="data[product]" :browser="browser"></product-header>

    <!-- TABS -->
    <el-tabs
        v-model="editorTabs"
        v-if="mounted">

      <!-- OPTIONS -->
      <el-tab-pane name="options" :label="$sp.print['Options']">
        <product-options
          @goToVariations="editorTabs='variations'"
          :data="data"
          :product="data[product]">
        </product-options>
      </el-tab-pane>

      <!-- DESCRIPTION -->
      <el-tab-pane name="description" :label="$sp.print['Description']">
        <product-description
          :product="data[product]">
        </product-description>
      </el-tab-pane>

      <!-- INVENTORY -->
      <el-tab-pane name="inventory" :label="$sp.print['Inventory']">
        <product-inventory
          :product="data[product]">
        </product-inventory>
      </el-tab-pane>

      <!-- VARIATIONS -->
      <el-tab-pane
          name="variations"
          v-if="data[product].type.value === 'variable'"
          :label="$sp.print['Variations']">
        <product-variations
            :product="data[product]"
            :changes="changes"
            :parent-id="data[product].id.value"
            :variation-ids="data[product].variations">
        </product-variations>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import ProductVariations from './Variations/Variations'
  import ProductWarnings from './Warnings/Warnings'
  import ProductHeader from './Header/Header'
  import ProductOptions from './Options/Options'
  import ProductDescription from './Description/Description'
  import ProductInventory from './Inventory/Inventory'
  export default {
    name: 'Product',
    components: {
      ProductVariations,
      ProductOptions,
      ProductWarnings,
      ProductHeader,
      ProductDescription,
      ProductInventory
    },
    props: [
      'data',
      'id',
      'browser',
      'product'
    ],
    data () {
      return {
        editorTabs: 'options',
        name: '',
        nameChanged: false,
        mounted: false,
        loading: false
      }
    },
    computed: {
      changes () {
        /** @todo - add check for any pending props in model and return 0 if true **/
        if (typeof this.data[this.product] !== 'undefined' && this.data[this.product] !== null) {
          if (typeof this.data[this.product].id !== 'undefined') {
            this.data[this.product].id.changed(false)
            if (this.data[this.product].date_on_sale_from.value === null) {
              this.data[this.product].date_on_sale_from.value = ''
              this.data[this.product].date_on_sale_from.changed(false)
            }
            if (this.data[this.product].date_on_sale_to.value === null) {
              this.data[this.product].date_on_sale_to.value = ''
              this.data[this.product].date_on_sale_to.changed(false)
            }
          }
          return (typeof this.data[this.product].changes === 'function' ? this.data[this.product].changes(false, true).length : 0)
        } else {
          return 0
        }
      }
    },
    mounted () {
      this.mounted = true
      this.init()
    },
    created () {
      if (typeof this.id !== 'undefined' && this.id !== null && this.id !== '') {
        this.data[this.product] = new this.$sp.models.Product(this.$pap.controller)
        this.data[this.product].id.value = this.id
        this.data[this.product].fetch().then(() => {
          this.init()
        })
      }
    },
    watch: {
      '$sp.data.on.refresh' () {
        this.data[this.product].fetch()
      },
      id (id) {
        if (this.data[this.product] !== null) {
          this.init()
        }
      },
      'changes' (changes) {
        this.$sp.data.changes = changes
      },
      '$sp.data.on.save' () {
        this.data[this.product].save()
      },
      'data.product.id.value' (val) {
        this.$sp.data.changes = this.changes
        if (val !== null) {
          this.init()
        }
      },
      'data.product.dimensions.value' (val) {
        if (val === null) {
          val = {
            width: 0,
            height: 0,
            length: 0
          }
        }
        if (this.$sp.data.autosave) { this.data.product.dimensions.save() }
      },
      'data.modal.dimensions.value' (val) {
        if (val === null) {
          val = {
            width: 0,
            height: 0,
            length: 0
          }
        }
        if (this.$sp.data.autosave) { this.data.modal.dimensions.save() }
      },
      '$sp.data.products.children' (models) {
        let scope = this
        if (
          typeof scope.id !== 'undefined' &&
          scope.id !== null &&
          scope.id !== '' &&
          typeof scope.data[scope.product] !== 'undefined' &&
          scope.data[scope.product].id.value !== scope.id
        ) {
          scope.data[scope.product] = new this.$sp.models.Product(this.$pap.controller)
          scope.data[scope.product].id.value = scope.id
          scope.data[scope.product].fetch().then(() => {
            scope.init()
          })
        } else {
          if (typeof this.data[this.product] !== 'undefined' && this.data[this.product] !== null && models !== null && models.constructor === Array && models.length > 0) {
            let prod = this.$sp.data.products.exchange(this.data[this.product])
            if (prod !== false) {
              this.data[this.product] = prod
            }
          } else if (this.data[this.product] === null && this.$sp.data.products.children.length > 0) {
            this.data[this.product] = this.$sp.data.products.children[0]
          }
        }
      }
    },
    methods: {
      init () {
        this.loading = true
        if (typeof this.data[this.product] !== 'undefined' && this.data[this.product] !== null) {
          let prod = this.$sp.data.products.exchange(this.data[this.product])
          if (prod !== false && (typeof this.id === 'undefined' || String(prod.id.value) === String(this.id))) {
            this.data[this.product] = prod
            this.loading = false
          } else if (typeof this.id !== 'undefined') {
            let prod = new this.$sp.models.Product(this.$pap.controller)
            prod.id.value = this.id
            prod.fetch().then(() => {
              this.data[this.product] = prod
              this.loading = false
            })
          }
          if (typeof this.data[this.product].date_modified === 'undefined' || this.data[this.product].date_modified.value === null) {
            this.data[this.product].fetch()
          }
        } else {
          this.loading = false
        }
      }
    }
  }
</script>

<style>
  .sp-selection .el-select-dropdown__item.selected {
    color: #000!important;
    background-color: #fff!important;
  }
  .sp-selection .el-select-dropdown__item.selected.sp-selected {
    color: #fff!important;
    background-color: #20a0ff!important;
  }
  .sp-selection .el-select-dropdown__item.selected.sp-selected {
    background-color: #1c8de0!important;
  }
  .sp-selection .el-select-dropdown__item.hover,
  .sp-selection .el-select-dropdown__item:hover {
    background-color: #e4e8f1!important;
  }
  .el-carousel__arrow i {
    width: 100%;
    left: 0;
  }
  .sp-featured-image {
    width: 74px;
    height: 74px;
    background-size: cover;
    background-color: #7da3bd;
  }
  .sp-options {
    width: 100%;
    border-collapse: collapse;
  }
  .sp-options tbody {
    width: 100%;
  }
  .sp-options td {
    padding: 10px 0;
    border-bottom: 1px solid #f1f1f1;
  }
  .sp-options td:first-child {
    width: 30%;
  }
  .sp-options td:last-child {
    width: 70%;
  }
</style>
