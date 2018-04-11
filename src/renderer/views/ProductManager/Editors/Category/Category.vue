<template>
  <el-col
    v-loading="(data[category] !== null && data[category].loading && data[data.browses] !== null)"
    class="sp-editor-wrapper">

    <span class="sp-editor-id" v-if="data[category] !== null && data[data.browses] !== null">
      #{{data[category].id.value}}
    </span>

    <div class="sp-sub-header" v-if="data[category] !== null && data[data.browses] !== null">
      <div
        class="sp-sub-header-image"
        v-loading="data[category].image.loading"
        :style="'background-image: url(' + (data[category].image.value !== null ? data[category].image.value.src_shop_thumbnail : '') + ')'"></div>
      <div class="sp-title-wrapper">
        <prop-title
            :placeholder="$sp.print['Title']"
            :prop="data[category].name"
            :autosave="$sp.data.autosave"
        ></prop-title>
        <ul class="actions" style="width: calc(100% - 74px - 20px);">
          <li><a :href="data[category].id.value !== null ? ($sp.server + '/wp-admin/term.php?taxonomy=product_cat&tag_ID=' + data[category].id.value + '&post_type=product') : '#'" target="_blank">{{$sp.print['Advanced']}}</a></li>
          <li @click="deleteForce = true">{{$sp.print['DeletePermanently']}}</li>
        </ul>
      </div>
    </div>

    <div class="sp-category-options sp-content-padding" v-if="data[category] !== null && data[data.browses] !== null">
      <table
          cellspacing="0"
          style="width: 100%; border-collapse: collapse"
          class="sp-options">
        <tbody style="width: 100%;">
          <tr>
            <td>
              <h3>{{$sp.print['CategorySlug']}}</h3>
            </td>
            <td>
              <el-input
                  v-model="data[category].slug.value"
                  v-loading="data[category].slug.loading"
                  @blur="$sp.data.autosave ? data[category].slug.save() : ''">
              </el-input>
            </td>
          </tr>
          <tr>
            <td>
              <h3>{{$sp.print['CategoryImage']}}</h3>
            </td>
            <td>
              <prop-image
                  :autosave="$sp.data.autosave"
                  :prop="data[category].image"
              ></prop-image>
            </td>
          </tr>
          <tr>
            <td>
              <h3>{{$sp.print['DisplayType']}}</h3>
            </td>
            <td>
              <el-select
                  v-loading="data[category].display.loading"
                  v-model="data[category].display.value"
                  @change="$sp.data.autosave ? data[category].display.save() : ''"
                  placeholder="Display">
                <el-option
                    :label="$sp.print['Default']"
                    value="default">
                </el-option>
                <el-option
                    :label="$sp.print['Products']"
                    value="products">
                </el-option>
                <el-option
                    :label="$sp.print['Subcategories']"
                    value="subcategories">
                </el-option>
                <el-option
                    :label="$sp.print['Both']"
                    value="both">
                </el-option>
              </el-select>
            </td>
          </tr>
          <h3>{{$sp.print['Description']}}</h3>
          <tr>
            <td colspan="2">
              <prop-textarea
                  :raw="data[category].description_raw"
                  :prop="data[category].description"
                  :autosave="$sp.data.autosave"
              ></prop-textarea>
            </td>
          </tr>
          <!--h3>Header Content</h3>
          <tr>
            <td colspan="2">
              <prop-textarea
                  :raw="data[category].header_raw"
                  :prop="data[category].header"
                  :autosave="$sp.data.autosave"
              ></prop-textarea>
            </td>
          </tr>
          <h3>Footer Content</h3>
          <tr>
            <td colspan="2">
              <prop-textarea
                  :raw="data[category].footer_raw"
                  :prop="data[category].footer"
                  :autosave="$sp.data.autosave"
              ></prop-textarea>
            </td>
          </tr-->
        </tbody>
      </table>
    </div>
    <el-dialog
        :title="$sp.print['DeletePermanently']"
        :visible.sync="deleteForce">
      <el-form>
        <span class="sp-delete-warning">{{$sp.print['ThisCanNotBeUndone']}}</span>
        <el-button @click="deleteForce = false">{{$sp.print['Cancel']}}</el-button>
        <el-button style="float: right" autofocus @click="remove(data[category])">{{$sp.print['Delete']}}</el-button>
      </el-form>
    </el-dialog>
  </el-col>
</template>

<script>
  import propMedia from '../../../../components/properties/PropMedia'
  import propTextarea from '../../../../components/properties/PropTextarea'
  import propImage from '../../../../components/properties/PropImage'
  import propTitle from '../../../../components/properties/PropTitle'
  export default {
    name: 'Category',
    props: [
      'data',
      'id',
      'category'
    ],
    components: {
      propMedia,
      propImage,
      propTextarea,
      propTitle
    },
    data () {
      return {
        settings: {},
        deleteForce: false
      }
    },
    created () {
      if (typeof this.id !== 'undefined' && this.id !== null && this.data[this.category] === null) {
        this.data[this.category] = new this.$sp.models.ProductCategory(this.$pap.controller, this.id)
        this.data[this.category].fetch()
      } else if (typeof this.id !== 'undefined' && this.id !== null && this.data[this.category] !== null && this.id !== this.data[this.category].id.value) {
        this.data[this.category].id.value = this.id
        this.data[this.category].fetch()
      }
    },
    watch: {
      '$sp.data.on.refresh' () {
        this.data[this.category].fetch()
      },
      '$sp.data.on.save' () {
        if (this.data[this.category] !== null) {
          this.data[this.category].save().then(() => {
            this.$sp.data.changes = this.data[this.category].changes(false, true).length
            this.data.refresh.categories = !this.data.refresh.categories
          })
        }
      },
      id (id) {
        if (typeof this.id !== 'undefined' && id !== null && this.data[this.category] === null) {
          this.data[this.category] = new this.$sp.models.ProductCategory(this.$pap.controller, id)
          this.data[this.category].fetch()
        } else if (typeof this.id !== 'undefined' && id !== null && this.data[this.category] !== null && id !== this.data[this.category].id.value) {
          this.data[this.category].id.value = id
          this.data[this.category].fetch()
        }
      },
      'data.category': {
        handler () {
          this.$sp.data.changes = this.detectChanges()
        },
        deep: true
      }
    },
    methods: {
      detectChanges () {
        if (this.data[this.category] !== null) {
          return this.data[this.category].changes(false, true).length
        }
      },
      selectImage () {
        this.$sp.mediaManager.getMedia('image', false, 'Select image', 'Choose').then(image => {
          if (image !== null) {
            this.data[this.category].image.value = {
              name: image.title,
              id: image.id,
              alt: image.alt_text
            }
            if (this.$sp.data.autosave) {
              this.data[this.category].image.save()
            }
          }
        })
      },
      getCategories (page = 1) {
        let categories = new this.$pap.List(this.$sp.models.ProductCategory, this.$pap.controller, null, { batch: 'batch' })
        let scope = this
        let fetch = () => {
          categories.query(false)
            .page(page)
            .order('asc')
            .custom('filter[orderby]=menu_order')
            .perPage(100)
            .fetch().then(() => {
              if (page < categories.pages) {
                page++
                fetch()
              } else {
                categories.sort('parent')
                scope.$sp.data.categories = categories
              }
            })
        }
        fetch()
      },
      remove (item) {
        item.remove(null, [{ key: 'force', value: true }]).then(() => {
          this.getCategories()
          if (this.$sp.data.categories.children.length > 0) {
            this.data[this.category] = this.$sp.data.categories.children[0]
          } else {
            this.data[this.category] = null
          }
          this.deleteForce = false
        })
      }
    }
  }
</script>
