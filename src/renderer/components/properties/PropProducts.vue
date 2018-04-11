<template>
  <div
      v-loading="prop.loading"
      :id="token + '-sp-cover'"
      :class="'sp-' + size"
      class="sp-products-list">
    <div
      @dragleave="removeDragover(token + '-sp-cover')"
      @dragenter="prevent($event); addDragover($event, token + '-sp-cover')"
      @touchend="removeDragover(token + '-sp-cover')"
      @touchmove="addDragover($event, token + '-sp-cover')"
      @dragover="prevent($event); addDragover($event, token + '-sp-cover')"
      @drop="dropped(); removeDragover(token + '-sp-cover')">
      <div
          class="sp-products-placeholder"
          v-if="prods.length === 0">
        {{$sp.print['DropProductHere']}}
      </div>
      <draggable
        v-model="prods"
        @end="dropEnd"
        :options="{scroll: true, group: {name: 'productIds', pull: 'clone', put: false}}">
        <div
            @dragover="prevent($event); addDragover($event, token + '-sp-cover')"
            @dragenter="prevent($event); addDragover($event, token + '-sp-cover')"
            @contextmenu.prevent="$refs.ctx.open($event, {product: prod, index: index})"
            v-for="(prod, index) in prods"
            class="sp-products-list-product">
          <grid-product
              :currencies="currencies"
              :product="prod">
          </grid-product>
          <el-button
              class="sp-delete-btn"
              @click="remove(index)"
              size="mini"
              type="danger"
              icon="el-icon-delete">
          </el-button>
        </div>
      </draggable>
      <context-menu
          style="position: fixed"
          class="sp-context-menu"
          ref="ctx"
          @ctx-open="ctxOpen">
        <li @click="remove(menu.index)">Remove</li>
        <li @click="$emit('open', menu.product)">Open</li>
      </context-menu>
    </div>
  </div>
</template>

<script>
  import draggable from 'vuedraggable'
  import gridProduct from '../Product'
  export default {
    props: [
      'prop',
      'products',
      'parent',
      'currencies',
      'autosave',
      'size'
    ],
    name: 'prop-products',
    components: {
      gridProduct,
      draggable
    },
    data () {
      return {
        token: Math.random().toString(36).substr(2, 5),
        prods: [],
        menu: null
      }
    },
    mounted () {
      this.init()
    },
    watch: {
      'prop.value' () {
        if (this.prop.changed()) {
          if (this.autosave) {
            this.prop.save().then(() => {
              this.products.fetch().then(() => {
                this.init()
              })
            })
          }
        }
      },
      'products.value.length' () {
        this.init()
      }
    },
    methods: {
      prevent (e) {
        e.preventDefault()
      },
      dropEnd (e) {
        document.dispatchEvent(new CustomEvent('sp-drop-product', {detail: e})) // Tell everyone I am dropped
      },
      dropped () {
        let handleProduct = (event) => {
          let e = event.detail
          let drop = {
            type: 'product',
            item: e.to.__vue__.value[e.newIndex],
            index: e.newIndex,
            preIndex: e.oldIndex,
            parent: e.to.__vue__.value,
            event: e
          }
          let product = drop.item.clone()
          if (this.prods.constructor === Array) {
            this.prods.push(product)
          } else {
            this.prods = [product]
          }
          this.prop.value = []
          this.prods.forEach(prod => {
            this.prop.value.push(prod.id.value)
          })
          if (this.autosave) {
            this.prop.save()
          }
        }
        document.addEventListener('sp-drop-product', handleProduct)
        setTimeout(function () {
          document.dispatchEvent(new CustomEvent('sp-drop-waiting-for-item'))
          document.removeEventListener('sp-drop-product', handleProduct)
        })
      },
      addDragover (e, id) {
        document.getElementById(id).classList.add('sp-dragover')
      },
      removeDragover (id) {
        document.getElementById(id).classList.remove('sp-dragover')
      },
      ctxOpen (data) {
        this.menu = data
      },
      init () {
        this.prods = []
        if (
          typeof this.products !== 'undefined' &&
          this.products !== null &&
          typeof this.products.value !== 'undefined' &&
          this.products.value !== null &&
          this.products.value.length > 0) {
          this.products.value.forEach(product => {
            let p = new this.$sp.models.Product(this.$pap.controller)
            p.set(product, true, true)
            this.prods.push(p)
          })
        }
      },
      remove (index) {
        let id = this.prop.value[index]
        this.prop.value.splice(index, 1)
        let i = 0
        index = -1
        this.prods.forEach(prod => {
          if (prod.id.value === id) {
            index = i
          }
          i++
        })
        if (index !== -1) {
          this.prods.splice(index, 1)
        }
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

<style>
  .sp-products-list {
    position: relative;
    display: block;
    padding: 10px 0 0 10px;
    margin-left: -10px;
    width: 100%;
  }
  .sp-products-list div {
    position: relative;
  }
  .sp-products-list .sp-delete-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 1000;
    padding: 5px;
    opacity: 0;
  }
  .sp-products-placeholder {
    opacity: .7;
  }
  .sp-products-list div:hover .sp-delete-btn {
    opacity: 1;
  }
  .sp-products-list-product {
    position: relative;
    width: 70px;
    height: 70px;
    font-size: .65em;
    float: left;
    margin: 0 10px 10px 0;
  }
  .sp-products.placeholder {
    font-size: .8em;
    color: #777;
    padding: 20px 0;
  }
  .sp-small.sp-products-list {
    height: 100%;
  }
  .sp-small .sp-products-list-product {
    width: 30px;
    height: 30px;
    margin: 0 3px 3px 0;
    text-overflow: unset;
  }
  .sp-small .sp-products-list-product .sp-delete-btn {
    top: 1px;
    left: 1px;
    padding: 1px;
  }
  .sp-small .sp-sale-label {
    padding: 1px;
    font-size: .7em;
    z-index: 1;
  }
  .sp-small .sp-products-placeholder {
    font-size: .8em!important;
    padding: 2px 0;
  }
  .sp-small .product .title .name {
    position: absolute;
    bottom: -3px;
    width: 100%;
    text-overflow: unset;
    padding: 1px;
    background: rgba(255, 255, 255, .7);
  }
  .sp-small .sp-rating .el-rate__icon {
    font-size: 8px;
  }
  .sp-small .el-rate {
    height: 10px;
  }
</style>
