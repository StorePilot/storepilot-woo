<template>
  <el-row class="sp-explorer-grid"
          :gutter="20"
          v-loading="$sp.data.products.loading">
    <div style="width: 100%; display: inline-block; margin-bottom: 20px">
      <div style="float: right">
        <div style="display: inline-block">
          <span style="margin-right: 10px">{{$sp.print['PerPage']}}</span>
          <el-select
            size="mini"
            v-model="$sp.data.storage.browser.perPage"
            style="margin-right: 10px; max-width: 80px"
            :placeholder="$sp.print['Items']">
            <el-option :label="6" :value="6"></el-option>
            <el-option :label="12" :value="12"></el-option>
            <el-option :label="24" :value="24"></el-option>
            <el-option :label="50" :value="50"></el-option>
            <el-option :label="100" :value="100"></el-option>
          </el-select>
        </div>
        <div style="display: inline-block">
          <span style="margin-right: 10px">{{$sp.print['PerRow']}}</span>
          <el-select
            style="max-width: 80px"
            size="mini"
            v-model="$sp.data.storage.browser.perRow"
            :placeholder="$sp.print['Show']">
            <el-option :label="2" :value="2"></el-option>
            <el-option :label="3" :value="3"></el-option>
            <el-option :label="4" :value="4"></el-option>
            <el-option :label="6" :value="6"></el-option>
          </el-select>
        </div>
      </div>
    </div>
    <div class="sp-grid-wrapper" v-loading="sorting">
      <div id="products">
        <draggable
          v-model="items"
          @end="dropEnd"
          :options="{scroll: true, group: {name: 'product', pull: 'clone', put: false}}">
          <el-col
              v-for="(prod, index) in items"
              :key="prod.id.value"
              :xs="Math.floor(24 / $sp.data.storage.browser.perRow)"
              :sm="Math.floor(24 / $sp.data.storage.browser.perRow)"
              :class="{
                'sp-product-selected': (
                  (
                    data.productClickedId === 0 &&
                    typeof data.product !== 'undefined' &&
                    data.product !== null &&
                    typeof data.product.id !== 'undefined' &&
                    data.product.id.value !== null &&
                    data.product.id.value === prod.id.value
                  ) ||
                  (
                    data.productClickedId === prod.id.value
                  ) ||
                  (
                    inBulk(prod.id.value)
                  )
                )
              }"
              class="col-product"
              :data-item-id="prod.id.value"
              data-item-type="product">
            <div
                @contextmenu.prevent="$refs.ctx.open($event, {product: prod})"
                @click="data.showEditor ? switchProd(prod) : (data.modal = prod, data.modalVisible = true)">
              <product
                  :currencies="$sp.data.currencies"
                  :product="prod"
              ></product>
            </div>
          </el-col>
        </draggable>
      </div>
      <context-menu
          class="sp-context-menu"
          ref="ctx"
          @ctx-open="ctxOpen">
        <li @click="switchProd(menu.product)">{{$sp.print['Edit']}}</li>
        <li @click="data.modal = menu.product; data.modalVisible = true">{{$sp.print['Open']}}</li>
        <li @click="view(menu.product.permalink.value)">{{$sp.print['View']}}</li>
        <li v-if="data.browses === 'category' && data.category !== null" @click="removeFromCategory(menu.product)">{{$sp.print['RemoveFromCategory']}}</li>
        <li @click="createDuplicate(menu.product)">{{$sp.print['Duplicate']}}</li>
        <li @click="advanced(menu.product.id.value)">{{$sp.print['Advanced']}}</li>
        <li @click="trash(menu.product)">{{$sp.print['Trash']}}</li>
        <li :title="$sp.print['ShortcutDelete']" @keyup.delete="deleteForce = true" @click="deleteForce = true">{{$sp.print['DeletePermanently']}}</li>
      </context-menu>
      <el-dialog title="Delete Product Permanently" :visible.sync="deleteForce">
        <el-form>
          <span class="sp-delete-warning">{{$sp.print['ThisCanNotBeUndone']}}</span>
          <el-button @click="deleteForce = false">{{$sp.print['Cancel']}}</el-button>
          <el-button style="float: right" autofocus @click="deletePermanently(menu.product)">{{$sp.print['Delete']}}</el-button>
        </el-form>
      </el-dialog>
      <el-dialog :title="$sp.print['DuplicateProduct']" :visible.sync="duplicateVisible">
        <el-form v-if="clone !== null" v-loading="clone.loading">
          <el-input
              @keyup.enter.native="duplicate"
              size="large"
              v-model="clone.name.value"
              :placeholder="$sp.print['Title']">
          </el-input>
          <el-input
              @keyup.enter.native="duplicate"
              style="margin-top: 20px;"
              v-model="clone.slug.value"
              :placeholder="$sp.print['Slug']">
          </el-input>
          <el-button
              style="margin-top: 20px;"
              @click="duplicateVisible = false">
            {{$sp.print['Close']}}
          </el-button>
          <el-button
              type="primary"
              @click="duplicate">
            {{$sp.print['Save']}}
          </el-button>
        </el-form>
      </el-dialog>
    </div>
  </el-row>
</template>

<script>
  import draggable from 'vuedraggable'
  import product from '../../../../components/Product'
  export default {
    components: {
      product,
      draggable
    },
    name: 'sp-grid',
    props: [
      'products',
      'data',
      'orderby'
    ],
    data () {
      return {
        sorting: false,
        items: [],
        menu: null,
        deleteForce: false,
        duplicateVisible: false,
        clone: null,
        multiselect: false
      }
    },
    created () {
      this.items = this.products
      window.onkeyup = (e) => {
        if (e.key === 'Meta' || e.keyCode === 17) {
          this.multiselect = false
        }
        // If product has focus, delete on delete button
        if (document.activeElement.classList.contains('product')) {
          // Delete
          if (e.keyCode === 46) {
            if (this.menu !== null) {
              this.menu.product = this.data.product
            } else {
              this.menu = {
                product: this.data.product
              }
            }
            this.deleteForce = true
          }
        }
      }
      window.onkeydown = (e) => {
        if (e.key === 'Meta' || e.keyCode === 17) {
          this.multiselect = true
        }
      }
    },
    watch: {
      products () {
        this.items = this.products
      },
      'clone.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.clone.slug.value = val
        }
      }
    },
    methods: {
      dropEnd (e) {
        let scope = this
        let handled = false
        let handledByOther = () => {
          handled = true
        }
        document.dispatchEvent(new CustomEvent('sp-drop-product', {detail: e})) // Give other handlers a chance
        document.addEventListener('sp-drop-waiting-for-item', handledByOther) // Check if other handlers waiting
        document.addEventListener('sp-drop-fetched-item', handledByOther) // Check if other handlers executed
        setTimeout(function () {
          document.removeEventListener('sp-drop-waiting-for-item', handledByOther) // To late, stop checking
          document.removeEventListener('sp-drop-fetched-item', handledByOther) // To late, stop checking
          if (!handled && e.newIndex !== e.oldIndex && scope.orderby === 'menu_order title') {
            // If no other handlers was executed, perform sort
            scope.sorting = true
            let order = new scope.$pap.Endpoint('orderProduct', scope.$pap.controller, 'sp')
            order.post('sp', null, [
              {
                key: 'product_id',
                value: e.to.__vue__.value[e.newIndex].id.value
              },
              {
                key: 'pre_id',
                value: e.newIndex !== 0 ? e.to.__vue__.value[(e.newIndex - 1)].id.value : 'false'
              },
              {
                key: 'next_id',
                value: e.to.__vue__.value.length > (e.newIndex + 1) ? e.to.__vue__.value[(e.newIndex + 1)].id.value : 'false'
              }
            ]).then(() => {
              scope.sorting = false
            })
          } else {
            // Else revert sorting
            e.to.__vue__.value.splice(e.oldIndex, 0, e.to.__vue__.value.splice(e.newIndex, 1)[0])
          }
        })
      },
      advanced (id) {
        let url = this.$sp.server
        url += '/wp-admin/post.php?post=' + id + '&action=edit'
        this.view(url)
      },
      view (url) {
        window.open(url, '_blank')
      },
      createDuplicate (prod) {
        this.duplicateVisible = true
        this.clone = prod.clone()
        this.clone.id.value = null
        this.clone.sku.value = null
        delete this.clone.upsell
        delete this.clone.cross_sell
        delete this.clone._links
        delete this.clone.date_created
        delete this.clone.date_created_gmt
        delete this.clone.date_modified
        delete this.clone.date_modified_gmt
        delete this.clone.description_raw
        delete this.clone.permalink
        delete this.clone.price_range
        delete this.clone.price_html
        if (this.clone.upsell_ids.value === null || this.clone.upsell_ids.value.length === 0) {
          this.clone.upsell_ids.value = 0
        }
        if (this.clone.cross_sell_ids.value === null || this.clone.cross_sell_ids.value.length === 0) {
          this.clone.cross_sell_ids.value = 0
        }
        if (this.clone.stock_quantity.value === null) {
          this.clone.stock_quantity.value = 0
        }
        if (typeof this.clone.description.value === 'string') {
          this.clone.description.value =
            this.clone.description.value
              .replace(/&#8211;/, '')
              .replace(/<\/p><p>/g, '')
              .replace(/<p>/g, '')
              .replace(/<\/p>/g, '')
        }
        if (typeof this.clone.short_description.value === 'string') {
          this.clone.short_description.value =
            this.clone.short_description.value
              .replace(/&#8211;/, '')
              .replace(/<\/p><p>/g, '')
              .replace(/<p>/g, '')
              .replace(/<\/p>/g, '')
        }
        if (typeof this.clone.purchase_note.value === 'string') {
          this.clone.purchase_note.value =
            this.clone.purchase_note.value
              .replace(/&#8211;/, '')
              .replace(/<\/p><p>/g, '')
              .replace(/<p>/g, '')
              .replace(/<\/p>/g, '')
        }
        if (typeof this.clone.name.value === 'string') {
          this.clone.name.value = this.clone.name.value + '-2'
        }
        if (this.clone.images.value !== 0) {
          this.clone.images.value.forEach(image => {
            if (image.id === 0) {
              this.clone.images.value = null
            } else {
              delete image.src
              delete image.src_thumbnail
              delete image.src_shop_thumbnail
              delete image.src_medium
              delete image.src_large
            }
          })
        }
      },
      duplicate () {
        this.clone.save().then(product => {
          this.duplicateVisible = false
          this.$emit('getProducts')
          this.data.product = product
          if (typeof product !== 'undefined') {
            this.data.productClickedId = product.id.value
          }
        })
      },
      trash (product) {
        product.remove().then(() => {
          this.$emit('getProducts')
        })
      },
      ctxOpen (data) {
        this.menu = data
      },
      switchProd (prod) {
        let prodid = (typeof this.$route.params.productId === 'undefined') ? prod.id.value : this.$route.params.productId
        if (!this.multiselect) {
          this.data.product = prod
          this.data.productClickedId = prod.id.value
          this.$sp.data.productsBulk.children = [this.data.product]
          prodid = prod.id.value
        } else {
          this.data.productClickedId = 0
          let found = false
          let i = 0
          this.$sp.data.productsBulk.children.forEach(child => {
            if (child.id.value === prod.id.value) {
              found = true
              this.$sp.data.productsBulk.children.splice(i, 1)
            }
            i++
          })
          if (!found) {
            this.data.product = prod
            this.$sp.data.productsBulk.children.push(prod)
          }
          if (this.$sp.data.productsBulk.children.length === 1) {
            this.data.product = this.$sp.data.productsBulk.children[0]
          }
        }
        if (this.$sp.data.productsBulk.children.length > 1) {
          this.$router.push({ name: 'Products' })
        } else {
          this.$router.push({
            name: 'Product',
            params: {
              id: prodid
            }
          })
        }
      },
      removeFromCategory (prod) {
        if (this.data.category !== null) {
          let i = 0
          prod.categories.value.forEach(cat => {
            if (cat.id === this.data.category.id.value) {
              prod.categories.value.splice(i, 1)
            }
            i++
          })
          prod.categories.save().then(() => {
            this.$emit('getProducts')
          })
        }
      },
      deletePermanently (prod) {
        prod.remove(null, [{ key: 'force', value: true }]).then(() => {
          this.$emit('getProducts')
          this.deleteForce = false
        })
      },
      inBulk (id) {
        let found = false
        this.$sp.data.productsBulk.children.forEach(child => {
          if (child.id.value === id) {
            found = true
          }
        })
        return found && this.$sp.data.productsBulk.children.length > 1
      }
    }
  }
</script>

<style>
  .col-product {
    height: 20%;
    padding: 0 10px 0 10px;
    margin-bottom: 20px;
  }
  .sp-grid-wrapper {
    display: flow-root;
  }
</style>
