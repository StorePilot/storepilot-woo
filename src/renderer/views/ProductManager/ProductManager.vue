<template>
  <div class="sp-browser-wrapper flex" v-if="authenticated">
    <navigation
        :browser="$refs.browser"
        :authenticated="authenticated"
        :data="data"></navigation>
    <browser
        ref="browser"
        :authenticated="authenticated"
        :data="data"></browser>
    <div
      class="sp-editor"
      :style="{
        'max-width': data.showEditor ? '640px' : '40px',
        overflow: data.showEditor ? 'auto' : 'hidden'
      }">
      <transition name="fade">
        <router-view
          style="margin-bottom: 20px"
          :style="{
            opacity: data.showEditor ? 1 : 0
          }"
          :data="data"
          :product="'product'"
          :category="'category'"
          :tag="'tag'"
          :browser="$refs.browser"
        ></router-view>
      </transition>
      <div
        class="sp-editor-toggle"
        :style="{
          width: data.showEditor ? '640px' : '40px'
       }">
        <el-button @click="data.showEditor=!data.showEditor" v-if="!data.showEditor" type="text">
          <i class="el-icon-d-arrow-left"></i>
        </el-button>
        <el-button @click="data.showEditor=!data.showEditor" v-else type="text">
          <i class="el-icon-d-arrow-right"></i>
        </el-button>
      </div>
    </div>
    <div style="clear:both"></div>
    <modal v-if="data.modalVisible" :browser="$refs.browser" :data="data" :product="'modal'"></modal>
    <modal-cat v-if="data.modalCatVisible" :data="data" :category="'modalCat'"></modal-cat>
    <modal-tag v-if="data.modalTagVisible" :data="data" :tag="'modalTag'"></modal-tag>
    <modal-settings v-if="data.modalSettingsVisible" :data="data"></modal-settings>
    <modal-bulk v-if="data.modalBulkVisible" :browser="$refs.browser" :data="data"></modal-bulk>
  </div>
</template>

<script>
  import Navigation from './Navigator/Navigator'
  import Browser from './Browser/Browser'
  import Modal from './Editors/Product/Modal'
  import ModalCat from './Editors/Category/Modal'
  import ModalTag from './Editors/Tag/Modal'
  import ModalSettings from './Editors/Settings/Modal'
  import ModalBulk from './Editors/Bulk/Modal'
  export default {
    name: 'ProductManager',
    components: {
      Navigation,
      Browser,
      Modal,
      ModalCat,
      ModalTag,
      ModalSettings,
      ModalBulk
    },
    props: [
      'authenticated'
    ],
    data () {
      return {
        data: {
          tab: 'categories',
          showEditor: true,
          showNav: true,
          editor: 'product',
          productClickedId: 0,
          modal: null,
          modalCat: null,
          modalTag: null,
          modalVisible: false,
          modalCatVisible: false,
          modalTagVisible: false,
          modalSettingsVisible: false,
          modalBulkVisible: false,
          product: null,
          category: null,
          tag: null,
          interval: null,
          timeout: null,
          browses: 'category',
          refresh: {
            tags: false,
            categories: false
          },
          mounted: false
        }
      }
    },
    mounted () {
      this.mounted = true
      this.init()
    },
    watch: {
      authenticated (valid) {
        if (valid) {
          this.init()
        }
      }
    },
    methods: {
      init () {
        if (this.authenticated) {
          // Fetch global settings and data
          this.$sp.data.taxClasses.fetch()
          this.$sp.data.shippingClasses.fetch()
          this.$sp.data.currencies.id.value = 'woocommerce_currency'
          this.$sp.data.currencies.fetch()
          this.$sp.data.productAttributes.fetch()
          let settings = new this.$pap.List(this.$sp.models.SettingsProduct, this.$pap.controller)
          settings.fetch().then(list => {
            list.children.forEach(setting => {
              this.$sp.data.settings[setting.id.value.replace('woocommerce_', '')] = setting
            })
          })
        }
      }
    }
  }
</script>

<style>
  .item-hover, .item-hover:after {
    background-color: #20a0ff;
  }
  .item-hover-disabled, .item-hover-disabled:after {
    background-color: transparent!important;
  }
  .col-product.item-hover, .col-product.item-hover:after {
    background-color: transparent;
  }
  .col-product.item-hover .product {
    filter: brightness(.9);
  }
  .sp-product-category-header > div > p,
  .sp-product-category-footer > div > p {
    margin: 0;
  }
  .fade-enter-active, .fade-leave-active {
    transition: transform .3s, opacity .3s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    transform: translateX(-20%);
    opacity: 0;
    position: absolute;
  }
  .sp-editor {
    position: relative;
  }
  .sp-editor-toggle {
    position: fixed;
    bottom: 0;
    background: rgba(249, 249, 249, 0.7);
  }
  .sp-editor-toggle button {
    margin: auto;
    width: 40px;
  }
</style>
