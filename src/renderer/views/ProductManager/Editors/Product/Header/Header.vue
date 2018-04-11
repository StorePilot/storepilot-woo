<template>
  <div>

    <!-- ID -->
    <span class="sp-editor-id">
      #{{product.id.value}}
    </span>

    <!-- HEADER -->
    <div class="sp-sub-header">

      <!-- IMAGE -->
      <div
        v-if="product.images.value !== 0 && product.images.value !== null"
        class="sp-sub-header-image"
        :style="product.images.value.length > 0 ? 'background-image: url(' + product.images.value[0].src_shop_thumbnail + ')' : ''">
      </div>

      <div class="sp-title-wrapper">

        <!-- TITLE -->
        <prop-title
          :placeholder="$sp.print['Title']"
          :prop="product.name"
          :autosave="$sp.data.autosave"
        ></prop-title>

        <!-- ACTIONS -->
        <ul class="actions">
          <li>
            <a
              :href="product.permalink.value !== null ? product.permalink.value : '#'"
              target="_blank">
              {{$sp.print['View']}}
            </a>
          </li>
          <li>
            <a
              :href="product.id.value !== null ? ($sp.server + '/wp-admin/post.php?post=' + product.id.value + '&action=edit') : '#'"
              target="_blank">
              {{$sp.print['Advanced']}}
            </a>
          </li>
          <li @click="createDuplicate">
            {{$sp.print['Duplicate']}}
          </li>
          <li @click="trash">
            {{$sp.print['Trash']}}
          </li>
        </ul>
      </div>

      <!-- MODALS -->
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
          <el-button type="primary" @click="duplicate">
            {{$sp.print['Save']}}
          </el-button>
        </el-form>
      </el-dialog>

    </div>
  </div>
</template>

<script>
  import propTitle from '../../../../../components/properties/PropTitle'
  export default {
    props: [
      'product',
      'browser'
    ],
    name: 'Header',
    components: {
      propTitle
    },
    data () {
      return {
        duplicateVisible: false,
        clone: null
      }
    },
    watch: {
      'clone.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.clone.slug.value = val
        }
      }
    },
    methods: {
      createDuplicate () {
        this.duplicateVisible = true
        this.clone = this.product.clone()
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
        if (this.clone.upsell_ids.value === null || this.clone.upsell_ids.value.length === 0) {
          this.clone.upsell_ids.value = 0
        }
        if (this.clone.cross_sell_ids.value === null || this.clone.cross_sell_ids.value.length === 0) {
          this.clone.cross_sell_ids.value = 0
        }
        if (this.clone.stock_quantity.value === null) {
          this.clone.stock_quantity.value = 0
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
        if (this.clone.status.value === 'trash') {
          this.clone.status.value = 'draft'
        }
        this.clone.save().then(product => {
          this.duplicateVisible = false
          this.browser.getProducts()
          this.product = product
          if (typeof product !== 'undefined') {
            this.productClickedId = product.id.value
          }
        })
      },
      trash () {
        this.product.remove().then(() => {
          this.product.fetch()
        }).catch(e => {
          this.product.fetch()
        })
      }
    }
  }
</script>

<style>
  .el-alert__content {
    width: 100%;
  }
  .sp-warning {
    margin: 5px;
    width: calc(100% - 10px);
  }
  .sp-warning button {
    position: absolute;
    right: 5px;
    top: 10px;
  }
</style>
