<template>
  <div class="sp-explorer">
    <div class="sp-sub-header">
        <div
            v-if="data.browses === 'category'"
            class="sp-sub-header-image"
            :style="'background-image: url(' + ((data[data.browses] !== null && data[data.browses].image.value !== null) ? data[data.browses].image.value.src_shop_thumbnail : '') + ')'">
        </div>
        <div class="sp-title-wrapper">
        <h2 class="sp-explorer-title">
          {{data[data.browses] === null ? $sp.print['All'] : (data[data.browses].id.value === null ? $sp.print['New'] : data[data.browses].name.value)}}
        </h2>
        <ul
            :style="data.browses === 'tag' ? 'margin-left: 0' : ''"
            class="actions">
          <li
            v-if="data[data.browses] !== null"
            @click="settingsAction()">
            {{data.browses === 'category' ? $sp.print['Category'] : $sp.print['Tag']}} {{$sp.print['Settings']}}
          </li>
          <!--li
            v-else
            @click="settingsAction(true)">
            {{$sp.print['ShopSettings']}}
          </li-->
          <li
              v-if="data[data.browses] !== null"
              @click="renameVisible = true">
            {{$sp.print['Rename']}}
          </li>
          <li
              v-if="data[data.browses] !== null">
            <a :href="typeof data[data.browses].permalink !== 'undefined' ? data[data.browses].permalink.value : '#'" target="_blank">
              {{$sp.print['View']}}
            </a>
          </li>
          <li v-if="data[data.browses] !== null"
              @click="createDuplicate">
            {{$sp.print['Duplicate']}}
          </li>
        </ul>
        </div>

        <el-dialog :title="$sp.print['Rename'] + ' ' + data.browses" :visible.sync="renameVisible">
          <el-form v-if="data[data.browses] !== null" v-loading="data[data.browses].loading">
            <el-input
                @keyup.enter.native="rename"
                size="large"
                v-model="data[data.browses].name.value"
                :placeholder="$sp.print['Title']"
            ></el-input>
            <el-input
                @keyup.enter.native="rename"
                style="margin-top: 20px;"
                v-model="data[data.browses].slug.value"
                :placeholder="$sp.print['Slug']"
            ></el-input>
            <el-button
                style="margin-top: 20px;"
                @click="renameVisible = false">
              {{$sp.print['Close']}}
            </el-button>
            <el-button
                type="primary"
                @click="rename">
              {{$sp.print['Save']}}
            </el-button>
          </el-form>
        </el-dialog>
        <el-dialog :title="$sp.print['Duplicate'] + ' ' + data.browses" :visible.sync="duplicateVisible">
          <el-form v-if="clone !== null" v-loading="clone.loading">
            <el-input
                @keyup.enter.native="duplicate"
                size="large"
                v-model="clone.name.value"
                :placeholder="$sp.print['Title']"
            ></el-input>
            <el-input
                @keyup.enter.native="duplicate"
                style="margin-top: 20px;"
                v-model="clone.slug.value"
                :placeholder="$sp.print['Slug']"
            ></el-input>
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
    </div><!-- .sub-header -->
    <div class="sp-explorer-search">
        <form @submit.prevent="getProducts" style="min-width: 180px">
          <el-input
            :placeholder="$sp.print['FilterSearch'] + ' ' + (searchFilter !== '' ? '( ' + searchFilter + ' )' : '')"
            style="width: 100%;"
            v-model="search">
            <el-button @click="getProducts" slot="append" icon="el-icon-search"></el-button>
          </el-input>
        </form>
        <el-select
              v-model="orderByValue"
              :placeholder="$sp.print['SortBy']">
            <el-option
                v-for="item in orderBy"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
        </el-select>

        <el-button-group style="margin-left: 10px; float: right">
          <el-button v-if="$sp.data.storage.browser.view!=='grid'" @click="$sp.data.storage.browser.view='grid'" icon="el-icon-tickets"></el-button>
          <el-button v-if="$sp.data.storage.browser.view!=='table'" @click="$sp.data.storage.browser.view='table'" icon="el-icon-menu"></el-button>
        </el-button-group>
    </div>
    <!--div
        :class="data.category.header.changed() ? 'editing' : ''"
        class="sp-category-content sp-product-category-header"
        v-if="data.category !== null">
      <div
          v-if="data.category.header.value"
          v-html="(
            data.category.header.changed() ||
            typeof data.category.header.html === 'undefined' ||
            data.category.header.html === ' ' ||
            data.category.header.html === '' ||
            data.category.header.html === null
            ) ? data.category.header.value : data.category.header.html"></div>
    </div-->
    <grid-view
      v-if="$sp.data.storage.browser.view !== 'table'"
      @getProducts="getProducts"
      :orderby="orderByValue"
      :data="data"
      :products="filteredProducts">
    </grid-view>
    <table-view
      v-else
      @getProducts="getProducts"
      :orderby="orderByValue"
      :data="data"
      :products="filteredProducts">
    </table-view>
    <!--div
        :class="data.category.footer.changed() ? 'editing' : ''"
        class="sp-category-content sp-product-category-footer"
        v-if="data.category !== null">
      <div
          v-if="data.category.footer.value"
          v-html="(
            data.category.footer.changed() ||
            typeof data.category.footer.html === 'undefined' ||
            data.category.footer.html === ' ' ||
            data.category.footer.html === '' ||
            data.category.footer.html === null
            ) ? data.category.footer.value : data.category.footer.html"></div>
    </div-->
    <div class="sp-explorer-footer">
      <div class="sp-explorer-footer-actions">
        <el-button
            style="margin-right: 20px"
            size="small"
            @click="createNewProduct">
          + {{$sp.print['Product']}}
        </el-button>
        <el-button
          v-if="!data.showEditor"
          :disabled="$sp.data.productsBulk.children.length < 1"
          style="margin-right: 20px"
          size="small"
          @click="data.modalBulkVisible = true">
          {{$sp.print['Actions']}}
        </el-button>
      </div>
      <el-dialog :title="$sp.print['AddProduct']" :visible.sync="createProductVisible">
        <el-form v-if="newProduct !== null" v-loading="newProduct.loading">
          <el-input
              @keyup.enter.native="createProduct"
              size="large"
              v-model="newProduct.name.value"
              :placeholder="$sp.print['Title']">
          </el-input>
          <el-input
              @keyup.enter.native="createProduct"
              style="margin-top: 20px;"
              v-model="newProduct.slug.value"
              :placeholder="$sp.print['Slug']">
          </el-input>
          <el-button
              style="margin-top: 20px;"
              @click="createProductVisible = false">
            {{$sp.print['Close']}}
          </el-button>
          <el-button
              type="primary"
              @click="createProduct">
            {{$sp.print['Save']}}
          </el-button>
        </el-form>
      </el-dialog>
        <div style="font-size: .9em;" class="link">
          <span style="font-size: 15px; font-weight: normal; color: #555;">
            {{ ((($sp.data.storage.browser.page - 1) * $sp.data.products.children.length) + 1) }}
            - {{ ((($sp.data.storage.browser.page - 1) * $sp.data.products.children.length) + $sp.data.products.children.length) }}
            / {{$sp.data.products.headers.unmapped['x-wp-total']}}
          </span>
        </div>
        <el-pagination
            layout="prev, pager, next"
            @current-change="pageSet"
            :page-size="$sp.data.storage.browser.perPage"
            :current-page="$sp.data.storage.browser.page"
            :total="Number($sp.data.products.headers.unmapped['x-wp-total'])">
        </el-pagination>
    </div>
  </div>
</template>

<script>
  import GridView from './Grid/Grid'
  import TableView from './Table/Table'
  export default {
    name: 'explorer',
    components: {
      GridView,
      TableView
    },
    props: [
      'authenticated',
      'data'
    ],
    data () {
      return {
        createProductVisible: false,
        renameVisible: false,
        duplicateVisible: false,
        newProduct: null,
        clone: null,
        search: '',
        searchFilter: '',
        pageChanged: false,
        startPage: 0,
        dragging: false,
        orderByValue: 'menu_order title',
        orderBy: [{
          value: 'menu_order title',
          label: this.$sp.print['CustomSorting']
        }, {
          value: 'title',
          label: this.$sp.print['Title']
        }, {
          value: 'date',
          label: this.$sp.print['Date']
        }, {
          value: 'id',
          label: this.$sp.print['Id']
        }, {
          value: 'slug',
          label: this.$sp.print['Slug']
        }],
        filteredProducts: []
      }
    },
    watch: {
      '$sp.data.on.refresh' () {
        this.getProducts()
      },
      authenticated (valid) {
        if (valid) {
          this.getProducts()
        }
      },
      'newProduct.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.newProduct.slug.value = val
        }
      },
      'clone.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.clone.slug.value = val
        }
      },
      'data.category.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.data.category.slug.value = val
        }
      },
      'data.tag.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.data.tag.slug.value = val
        }
      },
      'data.category.id.value' (id) {
        if (this.authenticated && Number(id) !== Number(this.$route.params.id)) {
          this.getProducts()
        }
        this.$sp.data.storage.browser.page = 1
      },
      'data.tag.id.value' (id) {
        if (this.authenticated && Number(id) !== Number(this.$route.params.id)) {
          this.getProducts()
        }
        this.$sp.data.storage.browser.page = 1
      },
      '$sp.data.storage.browser.perPage' () {
        if (this.authenticated) {
          this.getProducts()
        }
      },
      '$sp.data.products.children.length' (length) {
        if (length > this.$sp.data.storage.browser.perPage) {
          this.getProducts()
        }
        this.filterProducts()
      },
      orderByValue () {
        if (this.authenticated) {
          this.getProducts()
        }
      }
    },
    mounted () {
      if (this.authenticated) {
        this.getProducts()
      }
    },
    methods: {
      settingsAction (all = false) {
        if (this.data.showEditor) {
          if (all) {
            this.$router.push({
              name: 'Settings'
            })
          } else {
            this.$router.push({
              name: this.data.browses === 'category' ? 'Category' : 'Tag',
              params: {
                id: this.data[this.data.browses].id.value
              }
            })
          }
        } else {
          if (all) {
            this.data.modalSettingsVisible = true
          } else {
            if (this.data.browses === 'category') {
              this.data.modalCat = this.data[this.data.browses]
              this.data.modalCatVisible = true
            } else {
              this.data.modalTag = this.data[this.data.browses]
              this.data.modalTagVisible = true
            }
          }
        }
      },
      setCategory (id) {
        let cat = this.$sp.data.categories.children.find(child => child.id.value === id)
        this.data.category = typeof cat !== 'undefined' ? cat : null
        this.getProducts()
      },
      filterProducts () {
        let filtered = []
        this.$sp.data.products.children.forEach(product => {
          if (typeof product !== 'undefined' && (this.search === null || product.name.value === null || product.name.value.toLowerCase().indexOf(this.search.toLowerCase()) !== -1)) {
            filtered.push(product)
          }
        })
        this.filteredProducts = filtered
      },
      duplicate () {
        this.clone.save().then(duplicate => {
          let refresh = this.data.browses === 'category' ? 'categories' : 'tags'
          this.data.refresh[refresh] = !this.data.refresh[refresh]
          this.duplicateVisible = false
          if (this.data.browses === 'category') {
            let products = new this.$pap.List(this.$sp.models.Product, this.$pap.controller, null, { batch: 'batch' })
            let scope = this
            let page = 1
            let fetch = () => {
              products.query(false)
                .page(page)
                .category(scope.data[scope.data.browses].id.value)
                .perPage(100)
                .fetch().then(() => {
                  if (page < products.headers.unmapped['x-wp-totalpages']) {
                    page++
                    fetch()
                  } else {
                    products.children.forEach(product => {
                      product.categories.value.push({id: duplicate.id.value})
                      product.categories.value.forEach(cat => {
                        delete cat.name
                        delete cat.slug
                      })
                    })
                    products.save().then(() => {
                      if (scope.data.product !== null) {
                        scope.data.product.categories.fetch()
                      }
                    })
                  }
                })
            }
            fetch()
          } else if (this.data.browses === 'tag') {
            let products = new this.$pap.List(this.$sp.models.Product, this.$pap.controller, null, { batch: 'batch' })
            let scope = this
            let page = 1
            let fetch = () => {
              products.query(false)
                .page(page)
                .tag(scope.data[scope.data.browses].id.value)
                .perPage(100)
                .fetch().then(() => {
                  if (page < products.headers.unmapped['x-wp-totalpages']) {
                    page++
                    fetch()
                  } else {
                    products.children.forEach(product => {
                      product.tags.value.push({id: duplicate.id.value})
                      product.tags.value.forEach(tag => {
                        delete tag.name
                        delete tag.slug
                      })
                    })
                    products.save().then(() => {
                      if (scope.data.product !== null) {
                        scope.data.product.tags.fetch()
                      }
                    })
                  }
                })
            }
            fetch()
          }
        })
      },
      rename () {
        this.data[this.data.browses].save().then(() => {
          let refresh = this.data.browses === 'category' ? 'categories' : 'tags'
          this.data.refresh[refresh] = !this.data.refresh[refresh]
          this.renameVisible = false
        })
      },
      createDuplicate () {
        this.clone = this.data[this.data.browses].clone()
        this.clone.name.value += '-2'
        this.clone.id.value = null
        this.clone.id.changed(false)
        if (this.data.browses === 'category') {
          delete this.clone.footer_raw
          delete this.clone.header_raw
          delete this.clone.description_raw
          if (this.clone.parent.value === 0) {
            delete this.clone.parent
          }
        }
        delete this.clone._links
        delete this.clone.permalink
        this.duplicateVisible = true
      },
      createNewProduct () {
        this.newProduct = new this.$sp.models.Product(this.$pap.controller)
        this.newProduct.status.value = 'draft'
        if (this.data.category !== null) {
          this.newProduct.categories.value = [{ id: this.data.category.id.value }]
        }
        if (this.data.tag !== null) {
          this.newProduct.tags.value = [{ id: this.data.tag.id.value }]
        }
        this.createProductVisible = true
      },
      createProduct () {
        this.newProduct.save().then(prod => {
          this.createProductVisible = false
          this.$sp.data.storage.browser.page = 1
          this.getProducts()
        })
      },
      pageDown () {
        if (this.$sp.data.storage.browser.page > 1) {
          this.$sp.data.storage.browser.page--
          this.getProducts()
          this.pageChanged = true
        }
      },
      pageUp () {
        if (this.$sp.data.storage.browser.page < this.$sp.data.products.headers.unmapped['x-wp-totalpages']) {
          this.$sp.data.storage.browser.page++
          this.getProducts()
          this.pageChanged = true
        }
      },
      pageSet (page) {
        if (page <= this.$sp.data.products.headers.unmapped['x-wp-totalpages'] && page >= 1) {
          this.$sp.data.storage.browser.page = page
          this.getProducts()
          this.pageChanged = true
        }
      },
      getProducts () {
        return new Promise((resolve, reject) => {
          if (
            (this.data.category !== null && this.data.category.id.value === null) ||
            (this.data.tag !== null && this.data.tag.id.value === null)
          ) {
            this.$sp.data.products.children = []
          } else {
            var makeRequest = () => {
              if (
                this.$sp.data.preload !== null &&
                typeof this.$sp.data.preload.products !== 'undefined' &&
                this.$sp.data.preload.products.length > 0 &&
                this.$sp.data.storage.browser.page === 1 &&
                this.orderByValue === 'menu_order title'
              ) {
                this.$sp.data.products.children = []
                let i = 0
                this.$sp.data.preload.products.forEach(prod => {
                  if (this.$sp.data.storage.browser.perPage > i) {
                    let product = new this.$pap.Endpoint(
                      this.$sp.data.products,
                      this.$pap.controller,
                      this.$sp.data.products.shared.defaultApi,
                      prod,
                      {multiple: false}
                    )
                    this.$sp.data.products.children.push(product)
                  }
                  i++
                })
                let total = this.$sp.data.preload.products.length
                this.$sp.data.products.headers.unmapped['x-wp-total'] = total
                this.$sp.data.products.headers.unmapped['x-wp-totalpages'] = Math.ceil(total / this.$sp.data.products.children.length)
                this.$sp.data.preload.products = []
                resolve(this.$sp.data.products)
              } else {
                this.searchFilter = this.search
                let query = this.$sp.data.products.query()
                  .page(this.$sp.data.storage.browser.page)
                  .search(this.searchFilter)
                  .order('asc')
                  .perPage(this.$sp.data.storage.browser.perPage)
                if (this.orderByValue === 'menu_order title') {
                  query.custom('filter[orderby]=' + this.orderByValue)
                } else {
                  query.orderby(this.orderByValue)
                }
                if (this.data.category !== null && this.data.category.id.value !== null) {
                  query.category(this.data.category.id.value)
                }
                if (this.data.tag !== null && this.data.tag.id.value !== null) {
                  query.tag(this.data.tag.id.value)
                }
                query.fetch().then(() => {
                  if (this.dragging && this.startPage < this.$sp.data.storage.browser.page) {
                    this.$sp.data.products.children.shift()
                  } else if (this.dragging && this.startPage > this.$sp.data.storage.browser.page) {
                    this.$sp.data.products.children.pop()
                  }
                  this.filterProducts()
                  if (this.data.product === null && this.$sp.data.products.children.length > 0) {
                    this.data.product = this.$sp.data.products.children[0]
                  }
                  resolve(this.$sp.data.products)
                }).catch(err => {
                  reject(err)
                })
              }
            }
            if (!this.$sp.data.loaded) {
              this.$watch('$sp.data.loaded', valid => {
                if (valid) {
                  makeRequest()
                } else {
                  reject(new Error('StorePilot data not loaded correctly'))
                }
              })
            } else {
              makeRequest()
            }
          }
        })
      }
    }
  }
</script>

<style>
  .sp-explorer{
    position: relative;
  }
  .sp-explorer-footer{
    display: flex;
    align-items: center;
    padding: 10px 0;
    z-index: 100;
    border-top: 1px solid #f1f1f1;
    background-color: rgba(255,255,255,.9);
  }
  .sp-explorer-footer-actions{
    margin-right: auto;
  }
  .sp-explorer-search {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
  }
  .sp-explorer-search > form{
    flex: 1;
    margin-right: 10px;
  }
  .sp-category-content{
    padding: 10px;
    background-color: #fff;
    border: 1px dashed #ddd;
    margin-bottom: 15px;
  }
  .sp-category-content:empty{
    display: none;
  }
  .actions {
    padding: 0;
    display: block;
    list-style-type: none;
    margin: 0;
  }
  .actions li {
    display: inline-block;
    margin-right: 7px;
    font-size: 13px;
    background-color: rgba(0,0,0,.03);
    padding: 4px 8px;
    border-radius: 99px;
  }
  .actions li, .actions li a {
    color: #20a0ff;
    cursor: pointer;
    text-decoration: none;
    user-select: none;
  }
  .actions li:hover, .actions li a:hover {
    color: rgba(32, 157, 255, 0.64);
  }
  .sp-hide-value input {
    color: transparent;
  }
</style>
