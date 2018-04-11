<template>
  <div v-loading="$sp.data.categories.loading" class="sp-navigator" :style="{
    width: data.showNav ? navWidthOpen : navWidthClosed
  }">
    <div v-if="data.showNav">
      <el-tabs v-if="mounted && authenticated" v-model="data.tab" @tab-click="tabChanged">
        <el-tab-pane :label="$sp.print['Categories']" name="categories">

          <el-menu ref="catMenu" id="sp-categories" default-active="0" class="el-menu-vertical-demo" style="background-color: transparent;">
            <el-menu-item id="categories-all" @click="setCategory('all')" index="0">{{$sp.print['All']}}</el-menu-item>
            <nested-menu
              :ctx="true"
              :refs="$refs"
              @item-click="setCategory"
              @item-drop="droppedOnCat"
              :key="categoriesNestedRender"
              :items="$sp.data.categoriesNested"></nested-menu>
            <div style="clear:both"></div>
          </el-menu>

          <el-dialog :title="$sp.print['AddCategory']" :visible.sync="newCategoryVisible">
            <el-form v-if="newCategoryModel !== null" v-loading="newCategoryModel.loading">
              <el-input
                  @keyup.enter.native="setCategory('new')"
                  size="large"
                  v-model="newCategoryModel.name.value"
                  :placeholder="$sp.print['CategoryTitle']">
              </el-input>
              <el-input
                  @keyup.enter.native="setCategory('new')"
                  v-model="newCategoryModel.slug.value"
                  style="margin-top: 20px;"
                  :placeholder="$sp.print['CategorySlug']">
              </el-input>
              <el-button
                  style="margin-top: 20px;"
                  @click="newCategoryVisible = false">
                {{$sp.print['Close']}}
              </el-button>
              <el-button
                  type="primary"
                  style="margin-top: 20px;"
                  @click="setCategory('new')">
                {{$sp.print['Create']}}
              </el-button>
            </el-form>
          </el-dialog>

        </el-tab-pane>
        <el-tab-pane :label="$sp.print['Tags']" name="tags">

          <el-menu
              ref="tagMenu"
              id="sp-tags"
              default-active="0"
              class="el-menu-vertical-demo"
              style="background-color: transparent;">
            <draggable
                v-model="$sp.data.tags.children"
                :options="{sort: false, scroll: true, group: {name: 'tag', pull: 'clone', put: false}}">
              <el-menu-item
                @click="setTag(tag)"
                v-if="typeof tag.count !== 'undefined'"
                v-for="(tag, index) in $sp.data.tags.children"
                :index="String(tag.id.value)"
                :data-item-id="tag.id.value"
                data-item-type="tag"
                :id="'tag-' + tag.id"
                class="sp-tag-item"
                :key="String(index + 1)">
                <div
                  @contextmenu.prevent="$refs.ctx.open($event, {type: 'tag', item: tag})"
                  class="sp-cover"
                  :id="tag.id.value + '-sp-cover'"
                  @dragleave="removeDragover(tag.id.value + '-sp-cover')"
                  @dragenter="addDragover(tag.id.value + '-sp-cover')"
                  @touchend="removeDragover(tag.id.value + '-sp-cover')"
                  @touchmove="addDragover(tag.id.value + '-sp-cover')"
                  @drop="droppedOnTag(tag, 'cover'); removeDragover(tag.id.value + '-sp-cover')"></div>
                <span class="sp-menu-item-label"> {{tag.name.value}} </span>
                <span class="sp-menu-item-count"> {{tag.count.value}} </span>
              </el-menu-item>
            </draggable>
            <div style="clear:both"></div>
          </el-menu>

          <el-dialog :title="$sp.print['AddTag']" :visible.sync="newTagVisible">
            <el-form v-if="newTagModel !== null" v-loading="newTagModel.loading">
              <el-input
                  @keyup.enter.native="setTag('new')"
                  size="large"
                  v-model="newTagModel.name.value"
                  :placeholder="$sp.print['TagTitle']">
              </el-input>
              <el-input
                  @keyup.enter.native="setTag('new')"
                  v-model="newTagModel.slug.value"
                  style="margin-top: 20px;"
                  :placeholder="$sp.print['TagSlug']"></el-input>
              <el-button
                  style="margin-top: 20px;"
                  @click="newTagVisible = false">
                {{$sp.print['Close']}}
              </el-button>
              <el-button
                  type="primary"
                  style="margin-top: 20px;"
                  @click="setTag('new')">
                {{$sp.print['Create']}}
              </el-button>
            </el-form>
          </el-dialog>

        </el-tab-pane>
      </el-tabs>
      <div style="clear:both"></div>
      <context-menu
          class="sp-context-menu"
          ref="ctx"
          @ctx-open="ctxOpen">
        <div v-if="menu !== null && menu.type === 'category'">
          <li @click="settings('Category', menu.item.id.value)">{{$sp.print['Edit']}}</li>
          <li @click="view(menu.item.permalink.value)">{{$sp.print['View']}}</li>
          <li @click="renameDialog('category', menu.item)">{{$sp.print['Rename']}}</li>
          <li @click="createDuplicate('category', menu.item)">{{$sp.print['Duplicate']}}</li>
          <li @click="advanced('category', menu.item.id.value)">{{$sp.print['Advanced']}}</li>
          <li @click="deleteForce = true">{{$sp.print['Delete']}}</li>
        </div>
        <div v-if="menu !== null && menu.type === 'tag'">
          <li @click="settings('Tag', menu.item.id.value)">{{$sp.print['Edit']}}</li>
          <li @click="view(menu.item.permalink.value)">{{$sp.print['View']}}</li>
          <li @click="renameDialog('tag', menu.item)">{{$sp.print['Rename']}}</li>
          <li @click="createDuplicate('tag', menu.item)">{{$sp.print['Duplicate']}}</li>
          <li @click="advanced('tag', menu.item.id.value)">{{$sp.print['Advanced']}}</li>
          <li @click="deleteForce = true">{{$sp.print['Delete']}}</li>
        </div>
      </context-menu>

      <el-dialog :title="$sp.print['Rename'] + ' ' + $sp.print[renaming]" :visible.sync="renameVisible">
        <el-form v-if="renamingItem !== null" v-loading="renamingItem.loading">
          <el-input
              @keyup.enter.native="rename"
              size="large"
              v-model="renamingItem.name.value"
              :placeholder="$sp.print['Title']"
          ></el-input>
          <el-input
              @keyup.enter.native="rename"
              style="margin-top: 20px;"
              v-model="renamingItem.slug.value"
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

      <el-dialog :title="$sp.print['Duplicate'] + ' ' + (duplicating === 'category' ? $sp.print['Category'] : $sp.print['Tag'])" :visible.sync="duplicateVisible">
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

      <el-dialog
          :title="$sp.print['DeletePermanently']"
          :visible.sync="deleteForce">
        <el-form>
          <span class="sp-delete-warning">{{$sp.print['ThisCanNotBeUndone']}}</span>
          <el-button @click="deleteForce = false">{{$sp.print['Cancel']}}</el-button>
          <el-button style="float: right" autofocus @click="remove(menu.type, menu.item)">{{$sp.print['Delete']}}</el-button>
        </el-form>
      </el-dialog>
    </div>

    <div class="sp-bottom-nav" :style="{
      width: data.showNav ? navWidthOpen : navWidthClosed
    }">
      <el-row>
        <el-col :sm="data.showNav ? 18 : 24">
          <el-button
              v-if="data.tab === 'categories'"
              type="text"
              :style="data.showNav ? 'text-align: left; width: calc(100% - 20px); padding-left: 20px' : 'width: 100%'"
              @click="newCategory">
            <i class="el-icon-plus"></i>{{ data.showNav ? (' ' + $sp.print['Category']) : ''}}
          </el-button>
          <el-button
              v-else
              type="text"
              :style="data.showNav ? 'text-align: left; width: calc(100% - 20px); padding-left: 20px' : 'width: 100%'"
              @click="newTag">
            <i class="el-icon-plus"></i>{{ data.showNav ? (' ' + $sp.print['Tag']) : ''}}
          </el-button>
        </el-col>
        <el-col :sm="data.showNav ? 6 : 24">
          <el-button @click="data.showNav=!data.showNav" v-if="!data.showNav" type="text" style="width: 100%">
            <i class="el-icon-d-arrow-right"></i>
          </el-button>
          <el-button @click="data.showNav=!data.showNav" v-else type="text" style="width: 100%">
            <i class="el-icon-d-arrow-left"></i>
          </el-button>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
  import draggable from 'vuedraggable'
  import nestedMenu from '../../../components/NestedMenu'
  export default {
    name: 'Navigator',
    components: {
      nestedMenu,
      draggable
    },
    props: [
      'data',
      'authenticated',
      'browser'
    ],
    data () {
      return {
        navWidthOpen: '230px',
        navWidthClosed: '40px',
        deleteForce: false,
        clone: null,
        duplicateVisible: false,
        duplicating: 'category',
        renamingItem: null,
        renameVisible: false,
        renaming: 'Category',
        newCategoryVisible: false,
        newTagVisible: false,
        referer: this,
        newCategoryModel: null,
        newTagModel: null,
        mounted: false,
        menu: null,
        categoriesNestedRender: false
      }
    },
    watch: {
      '$sp.data.on.refresh' () {
        this.getCategories()
        this.getTags()
      },
      '$sp.data.loaded' (loaded) {
        if (loaded) {
          this.getCategories()
        }
      },
      'renamingItem.name.value' (val) {
        if (typeof val !== 'undefined' && val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.renamingItem.slug.value = val
        }
      },
      'newCategoryModel.name.value' (val) {
        if (val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.newCategoryModel.slug.value = val
        }
      },
      'newTagModel.name.value' (val) {
        if (val !== null) {
          val = val.replace(/ /g, '-').toLowerCase()
          this.newTagModel.slug.value = val
        }
      },
      authenticated (valid) {
        if (valid && this.mounted) {
          this.getCategories()
          this.getTags()
        }
      },
      mounted (valid) {
        if (valid && this.authenticated) {
          this.getCategories()
          this.getTags()
        }
        let cat = this.data.category
        if (typeof cat !== 'undefined' && cat !== null) {
          this.$nextTick(() => {
            this.$refs.catMenu.activeIndex = String(cat.id.value)
          })
        } else {
          this.$nextTick(() => {
            this.$refs.catMenu.activeIndex = '0'
          })
        }
        let tag = this.data.tag
        if (typeof tag !== 'undefined' && tag !== null) {
          this.$nextTick(() => {
            this.$refs.tagMenu.activeIndex = String(tag.id.value)
          })
        } else {
          this.$nextTick(() => {
            this.$refs.tagMenu.activeIndex = '0'
          })
        }
      },
      'data.refresh.tags' () {
        this.getTags()
      },
      'data.refresh.categories' () {
        this.getCategories()
      },
      'data.category.id.value' (id) {
        if (typeof id !== 'undefined' && id !== null) {
          this.$nextTick(() => {
            this.$refs.catMenu.activeIndex = String(id)
          })
        } else {
          this.$nextTick(() => {
            this.$refs.catMenu.activeIndex = '0'
          })
        }
      },
      'data.tag.id.value' (id) {
        if (typeof id !== 'undefined' && id !== null) {
          this.$nextTick(() => {
            this.$refs.tagMenu.activeIndex = String(id)
          })
        } else {
          this.$nextTick(() => {
            this.$refs.tagMenu.activeIndex = '0'
          })
        }
      }
    },
    mounted () {
      this.mounted = true
    },
    methods: {
      nestCategories (categories) {
        let nestByParents = (items, parentSelector = 'parent') => {
          let resolved = []
          let unresolved = []
          items.forEach(item => {
            let clone = item.clone()
            if (clone[parentSelector].value === 0) {
              resolved.push(clone)
            } else {
              unresolved.push(clone)
            }
          })
          let resolve = (resolved, unresolved) => {
            resolved.forEach(parent => {
              let children = []
              unresolved.forEach(cat => {
                children.push(cat.clone())
              })
              unresolved = []
              children.forEach(child => {
                if (parent.id.value === child[parentSelector].value) {
                  parent.children.push(child)
                } else {
                  unresolved.push(child)
                }
              })
              unresolved = resolve(parent.children, unresolved)
            })
            return unresolved
          }
          resolve(resolved, unresolved)
          return resolved
        }
        this.$sp.data.categoriesNested = nestByParents(categories)
        this.categoriesNestedRender = !this.categoriesNestedRender
      },
      addDragover (id) {
        document.getElementById(id).classList.add('sp-dragover')
      },
      removeDragover (id) {
        document.getElementById(id).classList.remove('sp-dragover')
      },
      droppedOnTag (item, area = 'cover') {
        let target = {
          type: 'tag',
          item: item,
          area: area,
          title: false,
          parent: this.$sp.data.tags
        }
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
          let product = drop.item
          let tag = target.item
          product.tags.value.push({ id: tag.id.value })
          product.tags.save().then(() => {
            this.$message({
              type: 'success',
              message: 'Added to tag'
            })
          })
        }
        document.addEventListener('sp-drop-product', handleProduct)
        setTimeout(function () {
          document.dispatchEvent(new CustomEvent('sp-drop-waiting-for-item'))
          document.removeEventListener('sp-drop-product', handleProduct)
        })
      },
      droppedOnCat (e) {
        if (e.drop !== null && e.target !== null) {
          if (e.drop.type === 'product' && e.target.area === 'cover') {
            let product = e.drop.item
            let category = e.target.item
            product.categories.value.push({id: category.id.value})
            product.categories.save().then(() => {
              this.$message({
                type: 'success',
                message: 'Added to category'
              })
            })
          } else if (e.drop.type === 'category') {
            if (e.drop.item.id.value !== e.target.item.id.value) {
              if (e.target.area === 'cover') {
                let cat = this.$sp.data.categories.exchange(e.drop.item)
                cat.parent.value = e.target.item.id.value
                cat.menu_order.value = 0
                cat.save().then(() => {
                  this.$sp.data.categories.sort('menu_order')
                  this.nestCategories(this.$sp.data.categories.children)
                })
              } else {
                let cat = this.$sp.data.categories.exchange(e.drop.item)
                cat.parent.value = e.target.item.parent.value
                let i = 0
                e.target.parent.forEach(parent => {
                  if (parent.id.value !== cat.id.value) {
                    parent = this.$sp.data.categories.exchange(parent)
                    if (e.target.area === 'top' && e.target.item.id.value === parent.id.value) {
                      cat.menu_order.value = i++
                      cat.menu_order.changed(true)
                    }
                    parent.menu_order.value = i++
                    parent.menu_order.changed(true)
                    if (e.target.area === 'bottom' && e.target.item.id.value === parent.id.value) {
                      cat.menu_order.value = i++
                      cat.menu_order.changed(true)
                    }
                  }
                })
                this.$sp.data.categories.save().then(() => {
                  this.$sp.data.categories.sort('menu_order')
                  this.nestCategories(this.$sp.data.categories.children)
                })
              }
            }
          }
        }
      },
      ctxOpen (data) {
        this.menu = data
      },
      createDuplicate (type, item) {
        this.duplicating = type
        if (type === 'category') {
          this.$sp.data.categories.children.forEach(cat => {
            if (cat.id.value === item.id.value) {
              this.clone = cat.clone()
            }
          })
          delete this.clone.footer_raw
          delete this.clone.header_raw
          delete this.clone.description_raw
          if (this.clone.parent.value === 0) {
            delete this.clone.parent
          }
        } else {
          this.clone = item.clone()
        }
        this.clone.name.value += '-2'
        delete this.clone._links
        delete this.clone.permalink
        this.duplicateVisible = true
      },
      duplicate () {
        let prevId = this.clone.id.value
        this.clone.id.value = null
        this.clone.id.changed(false)
        this.clone.save().then(duplicate => {
          if (this.duplicating === 'category') {
            this.getCategories()
          } else {
            this.getTags()
          }
          this.duplicateVisible = false
          if (this.duplicating === 'category') {
            let products = new this.$pap.List(this.$sp.models.Product, this.$pap.controller, null, { batch: 'batch' })
            let page = 1
            let fetch = () => {
              products.query(false)
                .page(page)
                .category(prevId)
                .perPage(100)
                .fetch().then(() => {
                  if (page < products.headers.unmapped['x-wp-totalpages']) {
                    page++
                    fetch()
                  } else {
                    products.children.forEach(product => {
                      if (typeof duplicate !== 'undefined') {
                        product.categories.value.push({id: duplicate.id.value})
                        product.categories.value.forEach(cat => {
                          delete cat.name
                          delete cat.slug
                        })
                      }
                    })
                    products.save().then(() => {
                      if (this.data.product !== null) {
                        this.data.product.categories.fetch()
                      }
                    })
                  }
                })
            }
            fetch()
          } else {
            let products = new this.$pap.List(this.$sp.models.Product, this.$pap.controller, null, { batch: 'batch' })
            let page = 1
            let fetch = () => {
              products.query(false)
                .page(page)
                .tag(prevId)
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
                      if (this.data.product !== null) {
                        this.data.product.tags.fetch()
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
        this.renamingItem.save().then(() => {
          this.renameVisible = false
          this.renamingItem = null
        })
      },
      renameDialog (type, item) {
        if (type === 'category') {
          this.$sp.data.categories.children.forEach(cat => {
            if (cat.id.value === item.id.value) {
              this.renaming = 'Category'
              this.renamingItem = cat
              this.renameVisible = true
            }
          })
        } else {
          this.renaming = 'Tag'
          this.renamingItem = item
          this.renameVisible = true
        }
      },
      remove (type, item) {
        if (type === 'category') {
          this.$sp.data.categories.children.forEach(cat => {
            if (cat.id.value === item.id.value) {
              cat.remove(null, [{ key: 'force', value: true }]).then(() => {
                this.getCategories()
                this.deleteForce = false
              })
            }
          })
        } else {
          item.remove(null, [{ key: 'force', value: true }]).then(() => {
            this.getTags()
            this.deleteForce = false
          })
        }
      },
      advanced (type, id) {
        let url = this.$sp.server
        if (type === 'tag') {
          url += '/wp-admin/term.php?taxonomy=product_tag&tag_ID=' + id + '&post_type=product'
        } else {
          url += '/wp-admin/term.php?taxonomy=product_cat&tag_ID=' + id + '&post_type=product'
        }
        this.view(url)
      },
      view (url) {
        window.open(url, '_blank')
      },
      settings (route, id) {
        this.$router.push({
          name: route,
          params: {
            id: id
          }
        })
      },
      newCategory () {
        this.newCategoryModel = new this.$sp.models.ProductCategory(this.$pap.controller)
        this.newCategoryVisible = !this.newCategoryVisible
      },
      newTag () {
        this.newTagModel = new this.$sp.models.ProductTag(this.$pap.controller)
        this.newTagVisible = !this.newTagVisible
      },
      tabChanged (tab) {
        this.data.browses = (tab.name === 'tags' ? 'tag' : 'category')
      },
      getCategory (id) {
        this.data.tag = null
        if (id === null || id === 'all') {
          this.data.category = null
        } else {
          if (this.data.category === null) {
            this.data.category = new this.$sp.models.ProductCategory(this.$pap.controller)
          }
          this.data.category.id.value = id
          this.data.category.fetch()
        }
      },
      getTag: (tag) => {
        this.data.category = null
        if (tag === null || tag === 'all') {
          this.data.tag = null
        } else {
          if (this.data.tag === null) {
            this.data.tag = new this.$sp.models.ProductTag(this.$pap.controller)
          }
          this.data.tag.id.value = tag
          this.data.tag.fetch()
        }
      },
      setCategory (item) {
        this.data.tag = null
        if (typeof item === 'string' && item === 'all') {
          this.data.category = null
          this.browser.getProducts()
        } else if (typeof item === 'string' && item === 'new') {
          this.data.category = this.newCategoryModel
          this.data.category.save().then(category => {
            this.newCategoryVisible = false
            this.getCategories()
            this.$router.push({
              name: 'Category',
              params: {
                id: category.id.value
              }
            })
            this.browser.getProducts()
          })
        } else {
          this.data.category = item
          this.browser.getProducts()
        }
      },
      setTag (item) {
        this.data.category = null
        if (typeof item === 'string' && item === 'new') {
          this.data.tag = this.newTagModel
          this.data.tag.save().then(tag => {
            this.newTagVisible = false
            this.getTags()
            this.$router.push({
              name: 'Tag',
              params: {
                id: tag.id.value
              }
            })
            this.browser.getProducts()
          })
        } else {
          this.data.tag = item
          this.browser.getProducts()
        }
      },
      getCategories (page = 1) {
        if (this.$sp.data.loaded) {
          if (
            this.$sp.data.preload !== null &&
            typeof this.$sp.data.preload.categories !== 'undefined' &&
            this.$sp.data.preload.categories.length > 0
          ) {
            this.$sp.data.categories.children = []
            this.$sp.data.preload.categories.forEach(cat => {
              let category = new this.$pap.Endpoint(
                this.$sp.data.categories,
                this.$pap.controller,
                this.$sp.data.categories.shared.defaultApi,
                cat,
                { multiple: false }
              )
              this.$sp.data.categories.children.push(category)
            })
            this.$sp.data.categories.sort('menu_order')
            this.nestCategories(this.$sp.data.categories.children)
            this.$sp.data.preload.categories = []
          } else {
            let categories = []
            this.$sp.data.categories.children = []
            let fetch = () => {
              this.$sp.data.categories.query()
                .page(page)
                .order('asc')
                .custom('filter[orderby]=menu_order title')
                .perPage(100)
                .fetch().then(() => {
                  categories = categories.concat(this.$sp.data.categories.children)
                  if (page < this.$sp.data.categories.headers.unmapped['x-wp-totalpages']) {
                    page++
                    fetch()
                  } else {
                    this.$sp.data.categories.children = categories
                    this.$sp.data.categories.sort('menu_order')
                    this.nestCategories(this.$sp.data.categories.children)
                  }
                })
            }
            fetch()
          }
        }
      },
      getTags (page = 1) {
        this.$sp.data.tags.children = []
        let fetch = () => {
          this.$sp.data.tags.query(false)
            .page(page)
            .order('asc')
            .custom('filter[orderby]=menu_order title')
            .perPage(100)
            .fetch().then(() => {
              if (page < this.$sp.data.tags.headers.unmapped['x-wp-totalpages']) {
                page++
                fetch()
              }
            })
        }
        fetch()
      }
    }
  }
</script>

<style>
  .fade-enter-active, .fade-leave-active {
    transition: transform .3s, opacity .3s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    transform: translateX(-20%);
    opacity: 0;
    position: absolute;
  }
  .sp-navigator .sp-cover {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .sp-navigator .sp-top {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 10px;
  }
  .sp-navigator .sp-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 10px;
  }
</style>
