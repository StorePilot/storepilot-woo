<template>
  <el-row
    class="sp-explorer-grid"
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
            <!--el-option :label="100" :value="100"></el-option-->
          </el-select>
        </div>
        <div style="display: inline-block">
          <span style="margin-right: 10px">{{$sp.print['Display']}}</span>
          <el-select
            class="sp-hide-value"
            multiple
            collapse-tags
            size="mini"
            v-model="$sp.data.storage.browser.display">
            <el-option
              v-for="item in displayOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </div>
      </div>
    </div>
    <div
      class="sp-table-wrapper"
      v-loading="sorting">
      <table class="sp-table el-table" :class="{sorting: (sortingCols || canSort)}">
        <thead>
          <draggable
            v-model="$sp.data.storage.browser.display"
            @end="sortProps($event); sortingCols=false"
            :options="{filter: '.sp-draggable-ignore', group: {name: 'props', pull: 'clone', put: false}}"
            :element="'tr'">
            <th class="sp-draggable-ignore sp-fixed-left">
              <el-checkbox v-model="checked" style="margin-left: 5px" name="type"></el-checkbox>
            </th>
            <th class="sp-draggable-ignore sp-fixed-left">{{$sp.print['Image']}}</th>
            <th
              style="cursor: move"
              @mouseenter="canSort=true"
              @mouseleave="canSort=false"
              v-if="value !== null && typeof value !== 'undefined'"
              v-for="value in $sp.data.storage.browser.display">
              {{$sp.print[value]}}
            </th>
            <th class="sp-draggable-ignore"></th>
            <th class="sp-draggable-ignore sp-fixed-right">{{$sp.print['Actions']}}</th>
          </draggable>
        </thead>
        <draggable
          v-model="items"
          @end="dropEnd"
          :element="'tbody'"
          :options="{handle: '.sp-table-handle', /*setData: modifyDragItem,*/ scroll: true, group: {name: 'product', pull: 'clone', put: false}}">
          <tr
            @contextmenu="ctxMenu($event, product)"
            v-for="(product, index) in items">
            <td class="sp-fixed-left sp-fixed-left-c1">
              <div class="sp-table-handle">
                <i class="el-icon-d-caret"></i>
              </div>
              <el-checkbox
                @change="check(product, index)"
                v-model="product.shared.checked"
                name="type"
                class="sp-table-checkbox"></el-checkbox>
            </td>
            <td class="sp-fixed-left sp-fixed-left-c2">
              <div
                @click="data.showEditor ? switchProd(product) : (data.modal = product, data.modalVisible = true)"
                v-lazy:background-image="product.images.value[0].src_shop_thumbnail"
                style="cursor: pointer; background-size: cover; width: 40px; height: 40px; background-repeat: no-repeat">
              </div>
            </td>
            <component
              v-if="value !== null && typeof value !== 'undefined'"
              v-for="value in $sp.data.storage.browser.display"
              :key="value"
              :product="product"
              :data="data"
              :is="'prop-' + value">
            </component>
            <td class="sp-table-padding"></td>
            <td class="sp-fixed-right">
              <el-button-group style="margin-top: 5px">
                <el-button
                  @click="view(product.permalink.value)"
                  size="mini"
                  icon="el-icon-view">
                </el-button>
                <el-button
                  v-loading="product.loading"
                  @click="product.save()"
                  :type="product.changes(false, true).length > 0 ? 'primary' : ''"
                  :disabled="product.changes(false, true).length < 1"
                  size="mini"
                  icon="el-icon-check">
                </el-button>
              </el-button-group>
            </td>
          </tr>
        </draggable>
      </table>
      <context-menu
        class="sp-context-menu"
        ref="ctx"
        @ctx-open="ctxOpen">
        <li @click="switchProd(menu.product);">{{$sp.print['Edit']}}</li>
        <li @click="data.modal = menu.product; data.modalVisible = true">{{$sp.print['Open']}}</li>
        <li @click="view(menu.product.permalink.value)">{{$sp.print['View']}}</li>
        <li v-if="data.browses === 'category' && data.category !== null" @click="removeFromCategory(menu.product)">{{$sp.print['RemoveFromCategory']}}</li>
        <li @click="createDuplicate(menu.product)">{{$sp.print['Duplicate']}}</li>
        <li @click="advanced(menu.product.id.value)">{{$sp.print['Advanced']}}</li>
        <li @click="trash(menu.product)">{{$sp.print['Trash']}}</li>
        <li :title="$sp.print['ShortcutDelete']" @keyup.delete="deleteForce = true" @click="deleteForce = true">{{$sp.print['DeletePermanently']}}</li>
      </context-menu>
      <el-dialog :title="$sp.print['DeleteProductPermanently']" :visible.sync="deleteForce">
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
  // @note - Below components should be equal to Prop{{value}} from displayOptions array
  import PropName from './Props/Name'
  import PropSlug from './Props/Slug'
  import PropPrice from './Props/Price'
  import PropSale from './Props/Sale'
  import PropStatus from './Props/Status'
  import PropType from './Props/Type'
  import PropCategories from './Props/Categories'
  import PropTags from './Props/Tags'
  import PropFeatured from './Props/Featured'
  import PropDownloadable from './Props/Downloadable'
  import PropUpsells from './Props/Upsells'
  import PropCrossSells from './Props/CrossSells'
  import PropVariations from './Props/Variations'
  import PropGallery from './Props/Gallery'
  import PropDescription from './Props/Description'
  import PropShortDescription from './Props/ShortDescription'
  import PropPurchaseNote from './Props/PurchaseNote'
  export default {
    components: {
      PropName,
      PropSlug,
      PropPrice,
      PropSale,
      PropStatus,
      PropType,
      PropCategories,
      PropTags,
      PropFeatured,
      PropDownloadable,
      PropUpsells,
      PropCrossSells,
      PropVariations,
      PropGallery,
      PropDescription,
      PropShortDescription,
      PropPurchaseNote,
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
        sortingCols: false,
        canSort: false,
        // @note - Every value need a component equal to Prop{{value}}. All values should start with uppercase
        displayOptions: [
          { label: this.$sp.print['Name'], value: 'Name' },
          { label: this.$sp.print['Slug'], value: 'Slug' },
          { label: this.$sp.print['Description'], value: 'Description' },
          { label: this.$sp.print['ShortDescription'], value: 'ShortDescription' },
          { label: this.$sp.print['PurchaseNote'], value: 'PurchaseNote' },
          { label: this.$sp.print['Price'], value: 'Price' },
          { label: this.$sp.print['Sale'], value: 'Sale' },
          { label: this.$sp.print['Status'], value: 'Status' },
          { label: this.$sp.print['Categories'], value: 'Categories' },
          { label: this.$sp.print['Tags'], value: 'Tags' },
          { label: this.$sp.print['Type'], value: 'Type' },
          { label: this.$sp.print['Variations'], value: 'Variations' },
          { label: this.$sp.print['Downloadable'], value: 'Downloadable' },
          { label: this.$sp.print['CrossSells'], value: 'CrossSells' },
          { label: this.$sp.print['Upsells'], value: 'Upsells' },
          { label: this.$sp.print['Featured'], value: 'Featured' },
          { label: this.$sp.print['Gallery'], value: 'Gallery' }
        ],
        menu: null,
        deleteForce: false,
        duplicateVisible: false,
        clone: null,
        sorting: false,
        items: [],
        checked: false,
        focus: -1,
        shift: false,
        dragImage: new Image()
      }
    },
    created () {
      let svg = require('../../../../assets/ic_flip_to_front_wp.svg')
      this.dragImage.src = svg
    },
    mounted () {
      this.setItems()
      window.onkeydown = (e) => {
        if (e.shiftKey) {
          this.shift = true
        }
      }
      window.onkeyup = (e) => {
        if (e.keyCode === 16 || e.key === 'Shift') {
          this.shift = false
        }
      }
    },
    watch: {
      checked (valid) {
        this.items.forEach(prod => {
          prod.shared.checked = valid
          if (valid) {
            this.bulksAdd(prod)
          } else {
            this.bulksRemove(prod)
          }
        })
      },
      products () {
        this.setItems()
        let checkedCount = 0
        this.items.forEach(prod => {
          if (prod.shared.checked) {
            checkedCount++
          }
        })
        if (checkedCount === 0) {
          this.checked = false
        }
      },
      '$sp.data.productsBulk.children.length' () {
        this.setItems()
      },
      'clone.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.clone.slug.value = val
        }
      }
    },
    methods: {
      modifyDragItem (dataTransfer) {
        dataTransfer.setDragImage(this.dragImage, 0, 0)
      },
      check (prod, index) {
        if (this.shift && this.focus !== -1 && this.focus !== index) {
          let start = index
          let end = this.focus
          let checked = prod.shared.checked
          if (end < start) {
            start = this.focus
            end = index
          }
          for (let i = start; i <= end; i++) {
            this.items[i].shared.checked = checked
            if (checked) {
              this.bulksAdd(this.items[i])
            } else {
              this.bulksRemove(this.items[i])
            }
          }
        } else {
          this.focus = index
          if (prod.shared.checked) {
            this.bulksAdd(prod)
          } else {
            this.bulksRemove(prod)
          }
        }
      },
      setItems () {
        let items = []
        this.products.forEach(prod => {
          prod.shared.checked = this.inBulks(prod)
          items.push(prod)
        })
        this.items = items
      },
      inBulks (prod) {
        return (typeof this.$sp.data.productsBulk.children
          .find(child => child.id.value === prod.id.value) !== 'undefined')
      },
      bulksAdd (prod) {
        if (typeof this.$sp.data.productsBulk.children
          .find(child => child.id.value === prod.id.value) === 'undefined') {
          this.$sp.data.productsBulk.children.push(prod)
        }
        if (this.$sp.data.productsBulk.children.length > 0) {
          this.$router.push({ name: 'Products' })
        }
      },
      bulksRemove (prod) {
        let children = []
        this.$sp.data.productsBulk.children.forEach(child => {
          if (child.id.value !== prod.id.value) {
            children.push(child)
          }
        })
        this.$sp.data.productsBulk.children = children
        if (this.$sp.data.productsBulk.children.length < 1) {
          this.$router.push({ name: 'Product' })
        }
      },
      sortProps (e) {
        let arr = []
        this.$sp.data.storage.browser.display.forEach(val => {
          if (val !== null && typeof val !== 'undefined') {
            arr.push(val)
          }
        })
        arr.splice((e.newIndex - 2), 0, arr.splice((e.oldIndex - 2), 1)[0])
        this.$sp.data.storage.browser.display = arr
      },
      ctxMenu (e, prod) {
        // Do not open custom ctx if Input, textarea, select etc.
        if (
          e.target.nodeName === 'DIV' ||
          e.target.nodeName === 'TR' ||
          e.target.nodeName === 'TD' ||
          e.target.nodeName === 'BUTTON'
        ) {
          e.preventDefault()
          this.$refs.ctx.open(e, {product: prod})
        }
      },
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
        this.data.product = prod
        this.data.productClickedId = prod.id.value
        prodid = prod.id.value
        this.$router.push({
          name: 'Product',
          params: {
            id: prodid
          }
        })
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
      }
    }
  }
</script>

<style>
  .sp-table-wrapper.el-loading-parent--relative {
    position: unset!important;
  }
  .sp-table-wrapper {
    width: 100%;
    overflow-x: scroll!important;
    overflow-y: visible!important;
  }
  .sp-table {
    position: inherit!important;
    margin: 0 250px 0 115px;
    padding-right: 150px;
    border-collapse: collapse;
  }
  .sp-table thead tr, .sp-table tbody tr {
    border-bottom: 1px solid #e6ebf5;
  }
  .sp-table tbody tr:hover:not(.sortable-ghost) {
    background: #f5f7fa;
  }
  .sp-table tbody tr:before {
    left: 0;
    width: 130px;
    position: absolute;
    content: ' ';
    border-top: 1px solid #e6ebf5;
    margin-top: -1px;
    z-index: 2;
  }
  .sp-table-wrapper td, .sp-table-wrapper th {
    padding: 10px!important;
    min-width: 120px!important;
  }
  .sp-table-padding {
    width: 120px;
    opacity: 0;
  }
  td.sp-fixed-left, td.sp-fixed-right {
    height: 61px;
  }
  .sp-fixed-left:first-child, .sp-fixed-left-c1 {
    background: #fff;
    position: absolute!important;
    left: 0!important;
    z-index: 2!important;
    min-width: 0!important;
    width: 50px;
  }
  .sp-fixed-left:first-child + .sp-fixed-left, .sp-fixed-left-c2 {
    background: #fff;
    position: absolute!important;
    left: 50px!important;
    z-index: 2!important;
    min-width: 75px!important;
    width: 75px!important;
  }
  .sp-fixed-left .sp-table-checkbox {
    padding: 10px 10px 13px 5px;
  }
  .sp-fixed-right {
    background: #fff;
    position: absolute!important;
    right: 0!important;
    min-width: 0!important;
    width: 110px;
    z-index: 2;
  }
  tbody tr:hover:not(.sortable-ghost) .sp-fixed-left, tbody tr:hover:not(.sortable-ghost) .sp-fixed-right {
    background: #f5f7fa!important;
  }
  .sorting .sp-draggable-ignore {
    pointer-events: none;
  }
  .sp-table-handle {
    position: absolute;
    left: -18px;
    top: 12px;
    cursor: move;
    opacity: 0;
    font-size: 2.5em;
    color: #d1d3d6;
  }
  tr:hover .sp-table-handle {
    opacity: .4;
  }
</style>
