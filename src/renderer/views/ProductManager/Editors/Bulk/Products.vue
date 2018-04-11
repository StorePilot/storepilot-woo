<template>
  <el-col
      class="sp-editor-wrapper"
      v-loading="$sp.data.productsBulk.loading || loading">

    <span class="sp-editor-id">
      {{$sp.print['Count']}}: {{$sp.data.productsBulk.children.length}}
    </span>

    <div class="sp-content-padding">
      <h2>{{$sp.print['BulkEdit']}}</h2>
      <h4>{{$sp.print['Update']}}</h4>
      <ul class="actions" style="width: calc(100% - 74px - 20px);">
        <li @click="doTrash">{{$sp.print['TrashAll']}}</li>
        <li @click="deleteForce = true">{{$sp.print['DeleteAllPermanently']}}</li>
      </ul>
      <el-row style="margin: 10px 0 40px">
        <el-col v-if="view==='menu'">
          <el-button-group>
            <el-button @click="view=editors.status">{{editors.status}}</el-button>
            <el-button @click="view=editors.title">{{editors.title}}</el-button>
            <el-button @click="view=editors.gallery">{{editors.gallery}}</el-button>
            <el-button @click="view=editors.price">{{editors.price}}</el-button>
            <el-button @click="view=editors.categories">{{editors.categories}}</el-button>
            <el-button @click="view=editors.tags">{{editors.tags}}</el-button>
            <el-button @click="view=editors.description">{{editors.description}}</el-button>
            <el-button @click="view=editors.purchaseNote">{{editors.purchaseNote}}</el-button>
            <el-button @click="view=editors.upsells">{{editors.upsells}}</el-button>
            <el-button @click="view=editors.crossSells">{{editors.crossSells}}</el-button>
            <el-button @click="view=editors.attributes">{{editors.attributes}}</el-button>
            <el-button @click="view=editors.stock">{{editors.stock}}</el-button>
            <el-button @click="view=editors.downloads">{{editors.downloads}}</el-button>
            <el-button @click="view=editors.shipping">{{editors.shipping}}</el-button>
          </el-button-group>
        </el-col>
        <el-col v-if="view!=='menu'">
          <el-card>
            <div slot="header" class="clearfix">
              <strong>{{view}}</strong>
              <el-button style="float: right; padding: 3px 0" type="text" @click="view='menu'">{{$sp.print['Close']}}</el-button>
            </div>
            <div v-if="view===editors.status">
              <el-form>
                <el-form-item>
                  <el-button-group>
                    <el-button @click="doPublish">{{$sp.print['Published']}}</el-button>
                    <el-button @click="doDraft">{{$sp.print['Draft']}}</el-button>
                    <el-button @click="doPending">{{$sp.print['Pending']}}</el-button>
                    <el-button @click="doPrivate">{{$sp.print['Private']}}</el-button>
                  </el-button-group>
                </el-form-item>
              </el-form>
            </div>
            <div v-if="view===editors.title">
              <el-form>
                <el-form-item>
                  <el-input v-model="title.value" :placeholder="$sp.print['Title']"></el-input>
                </el-form-item>
                <el-form-item>
                  <el-button-group>
                    <el-button @click="doReplace('name', title.value)">{{$sp.print['Replace']}}</el-button>
                    <el-button @click="doAppend('name', title.value)">{{$sp.print['Append']}}</el-button>
                    <el-button @click="doPrepend('name', title.value)">{{$sp.print['Prepend']}}</el-button>
                  </el-button-group>
                </el-form-item>
              </el-form>
              <el-form>
                <el-form-item>
                  <el-input v-model="slug.value" :placeholder="$sp.print['Slug']"></el-input>
                </el-form-item>
                <el-form-item>
                  <el-button-group>
                    <el-button @click="doReplace('slug', slug.value)">{{$sp.print['Replace']}}</el-button>
                    <el-button @click="doAppend('slug', slug.value)">{{$sp.print['Append']}}</el-button>
                    <el-button @click="doPrepend('slug', slug.value)">{{$sp.print['Prepend']}}</el-button>
                  </el-button-group>
                </el-form-item>
              </el-form>
            </div>
            <div v-if="view===editors.gallery">
              <el-form>
                <el-form-item>
                  <prop-gallery
                    :prop="gallery"
                    :autosave="false"
                  ></prop-gallery>
                </el-form-item>
                <el-form-item>
                  <el-button-group>
                    <el-button @click="doReplace('images', gallery.value)">{{$sp.print['Replace']}}</el-button>
                    <el-button @click="doMerge('images', gallery.value)">{{$sp.print['Append']}}</el-button>
                    <el-button @click="doPreMerge('images', gallery.value)">{{$sp.print['Prepend']}}</el-button>
                  </el-button-group>
                </el-form-item>
              </el-form>
            </div>
            <div v-if="view===editors.price">
              <el-col :lg="12" :sm="24">
                <el-form>
                  <h3>{{$sp.print['RegularPrice']}}</h3>
                  <el-form-item>
                    <el-select v-model="regularPriceBy" :placeholder="$sp.print['By']">
                      <el-option value="fixed" :label="$sp.print['Fixed']"></el-option>
                      <el-option value="percentage" :label="$sp.print['Percentage']"></el-option>
                    </el-select>
                  </el-form-item>
                  <el-form-item>
                    <prop-number
                        :autosave="false"
                        :prop="regularPrice"
                    ></prop-number>
                  </el-form-item>
                  <el-form-item>
                    <el-button-group>
                      <el-button v-if="regularPriceBy!=='percentage'" @click="doReplace('regular_price', regularPrice.value)">{{$sp.print['Replace']}}</el-button>
                      <el-button @click="doIncrease('regular_price', regularPrice.value, regularPriceBy === 'percentage')">{{$sp.print['Increase']}}</el-button>
                      <el-button @click="doDecrease('regular_price', regularPrice.value, regularPriceBy === 'percentage')">{{$sp.print['Decrease']}}</el-button>
                    </el-button-group>
                  </el-form-item>
                </el-form>
              </el-col>
              <el-col :lg="12">
                <el-form>
                  <h3>{{$sp.print['SalePrice']}}</h3>
                  <el-form-item>
                    <el-select v-model="salePriceBy" :placeholder="$sp.print['By']">
                      <el-option value="fixed" :label="$sp.print['Fixed']"></el-option>
                      <el-option value="percentage" :label="$sp.print['Percentage']"></el-option>
                    </el-select>
                  </el-form-item>
                  <el-form-item>
                    <prop-number
                        :autosave="false"
                        :prop="salePrice"
                    ></prop-number>
                  </el-form-item>
                  <el-form-item>
                    <el-button-group>
                      <el-button v-if="salePriceBy!=='percentage'" @click="doReplace('sale_price', salePrice.value)">{{$sp.print['Replace']}}</el-button>
                      <el-button @click="doIncrease('sale_price', salePrice.value, salePriceBy === 'percentage')">{{$sp.print['Increase']}}</el-button>
                      <el-button @click="doDecrease('sale_price', salePrice.value, salePriceBy === 'percentage')">{{$sp.print['Decrease']}}</el-button>
                    </el-button-group>
                  </el-form-item>
                </el-form>
              </el-col>
            </div>
            <div v-if="view===editors.categories">
              <prop-categories
                  :prop="categories"
                  :categories="$sp.data.categories"
                  :categoriesNested="$sp.data.categoriesNested"
                  :autosave="false"
              ></prop-categories>
              <el-button-group style="margin-top: 20px">
                <el-button @click="doReplace('categories', categories.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('categories', categories.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doRemove('categories', categories.value)">{{$sp.print['Remove']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.tags" class="text item">
              <prop-tags
                  :prop="tags"
                  :tags="$sp.data.tags.children"
                  :autosave="false"
              ></prop-tags>
              <el-button-group style="margin-top: 20px">
                <el-button @click="doReplace('tags', tags.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('tags', tags.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doRemove('tags', tags.value)">{{$sp.print['Remove']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.description">
              <strong>{{$sp.print['ShortDescription']}}</strong>
              <prop-textarea
                style="margin-top: 10px"
                :placeholder="$sp.print['StartComposing']"
                :prop="shortDescription"
                :autosave="false"
              ></prop-textarea>
              <el-button-group style="margin-top: 20px">
                <el-button @click="doReplace('short_description', shortDescription.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doAppend('short_description', shortDescription.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPrepend('short_description', shortDescription.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
              <strong style="display: block; margin-top: 20px">{{$sp.print['Description']}}</strong>
              <prop-textarea
                  style="margin-top: 10px"
                  :placeholder="$sp.print['StartComposing']"
                  :prop="description"
                  :autosave="false"
              ></prop-textarea>
              <el-button-group style="margin-top: 20px">
                <el-button @click="doReplace('description', description.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doAppend('description', description.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPrepend('description', description.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.purchaseNote">
              <prop-textarea
                  style="margin-top: 10px"
                  :placeholder="$sp.print['StartComposing']"
                  :prop="purchaseNote"
                  :autosave="false"
              ></prop-textarea>
              <el-button-group style="margin-top: 20px">
                <el-button @click="doReplace('purchase_note', purchaseNote.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doAppend('purchase_note', purchaseNote.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPrepend('purchase_note', purchaseNote.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.upsells">
              <prop-products
                :data="data"
                :prop="upsells"
                :currencies="$sp.data.currencies"
                :autosave="false"
              ></prop-products>
              <el-button-group style="display: block; margin-top: 20px">
                <el-button @click="doReplace('upsell_ids', upsells.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('upsell_ids', upsells.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPreMerge('upsell_ids', upsells.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.crossSells">
              <prop-products
                  :data="data"
                  :prop="crossSells"
                  :currencies="$sp.data.currencies"
                  :autosave="false"
              ></prop-products>
              <el-button-group style="display: block; margin-top: 20px">
                <el-button @click="doReplace('cross_sell_ids', crossSells.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('cross_sell_ids', crossSells.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPreMerge('cross_sell_ids', crossSells.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.attributes">
              <prop-product-attributes
                :autosave="$sp.data.autosave"
                :prop="attributes"
                :attributes="$sp.data.productAttributes"
                :terms="$sp.data.productAttributeTerms"
              ></prop-product-attributes>
              <el-button-group style="display: block; margin-top: 20px">
                <el-button @click="doReplace('attributes', attributes.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('attributes', attributes.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPreMerge('attributes', attributes.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.stock">
              <strong style="display: block; margin-bottom: 10px">{{$sp.print['Featured']}}</strong>
              <prop-switch
                  style="margin-right: 20px"
                  :prop="featured"
                  :autosave="false"
              ></prop-switch>
              <el-button size="mini" @click="doReplace('featured', featured.value)">{{$sp.print['Apply']}}</el-button>

              <strong style="display: block; margin: 20px 0">{{$sp.print['Virtual']}}</strong>
              <prop-switch
                  style="margin-right: 20px"
                  :prop="virtual"
                  :autosave="false"
              ></prop-switch>
              <el-button size="mini" @click="doReplace('virtual', virtual.value)">{{$sp.print['Apply']}}</el-button>

              <strong style="display: block; margin: 20px 0">{{$sp.print['ManageStock']}}</strong>
              <prop-switch
                  style="margin-right: 20px"
                  :prop="manageStock"
                  :autosave="false"
              ></prop-switch>
              <el-button size="mini" @click="doReplace('manage_stock', manageStock.value)">{{$sp.print['Apply']}}</el-button>

              <strong style="display: block; margin: 20px 0">{{$sp.print['StockQuantity']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="stockQuantity"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('stock_quantity', stockQuantity.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('stock_quantity', stockQuantity.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('stock_quantity', stockQuantity.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>

              <strong style="display: block; margin: 20px 0">{{$sp.print['AllowBackorders']}}</strong>
              <el-select v-model="allowBackorders.value" :placeholder="$sp.print['Select']" style="margin-right: 20px">
                <el-option value="no" :label="$sp.print['No']"></el-option>
                <el-option value="notify" :label="$sp.print['Notify']"></el-option>
                <el-option value="yes" :label="$sp.print['Yes']"></el-option>
              </el-select>
              <el-button @click="doReplace('backorders', allowBackorders.value)">{{$sp.print['Apply']}}</el-button>

              <strong style="display: block; margin: 20px 0">{{$sp.print['SoldIndividually']}}</strong>
              <prop-switch
                  style="margin-right: 20px"
                  :prop="soldIndividually"
                  :autosave="false"
              ></prop-switch>
              <el-button size="mini" @click="doReplace('sold_individually', soldIndividually.value)">{{$sp.print['Apply']}}</el-button>
            </div>
            <div v-if="view===editors.downloads" >
              <strong style="display: block; margin: 20px 0">{{$sp.print['Downloadable']}}</strong>
              <prop-switch
                  style="margin-right: 20px"
                  :prop="downloadable"
                  :autosave="false"
              ></prop-switch>
              <el-button size="mini" @click="doReplace('downloadable', downloadable.value)">{{$sp.print['Apply']}}</el-button>

              <strong style="display: block; margin: 20px 0">{{$sp.print['DownloadableFiles']}}</strong>
              <prop-downloads
                  :prop="downloads"
                  :autosave="false">
              </prop-downloads>
              <el-button-group style="display: block; margin-top: 20px">
                <el-button @click="doReplace('downloads', downloads.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doMerge('downloads', downloads.value)">{{$sp.print['Append']}}</el-button>
                <el-button @click="doPreMerge('downloads', downloads.value)">{{$sp.print['Prepend']}}</el-button>
              </el-button-group>

              <strong style="display: block; margin: 20px 0">{{$sp.print['DownloadLimit']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="downloadLimit"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('download_limit', downloadLimit.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('download_limit', downloadLimit.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('download_limit', downloadLimit.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>

              <strong style="display: block; margin: 20px 0">{{$sp.print['DownloadExpiry']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="downloadExpiry"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('download_expiry', downloadExpiry.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('download_expiry', downloadExpiry.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('download_expiry', downloadExpiry.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>
            </div>
            <div v-if="view===editors.shipping" class="text item">
              <strong style="display: block; margin: 0 0 20px">{{$sp.print['Weight']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="weight"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('weight', weight.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('weight', weight.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('weight', weight.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>
              <strong style="display: block; margin: 20px 0">{{$sp.print['Width']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="width"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('width', width.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('width', width.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('width', width.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>
              <strong style="display: block; margin: 20px 0">{{$sp.print['Height']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="height"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('height', height.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('height', height.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('height', height.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>
              <strong style="display: block; margin: 20px 0">{{$sp.print['Length']}}</strong>
              <prop-number
                  style="margin-right: 20px"
                  :prop="length"
                  :autosave="false"></prop-number>
              <el-button-group>
                <el-button @click="doReplace('length', length.value)">{{$sp.print['Replace']}}</el-button>
                <el-button @click="doIncrease('length', length.value)">{{$sp.print['Increase']}}</el-button>
                <el-button @click="doDecrease('length', length.value)">{{$sp.print['Decrease']}}</el-button>
              </el-button-group>
            </div>
          </el-card>
        </el-col>
      </el-row>
      <h4>{{$sp.print['Selected']}}</h4>
      <el-row>
        <el-col>
          <el-button style="margin-bottom: 10px" size="mini" @click="$sp.data.productsBulk.children = []">{{$sp.print['UnselectAll']}}</el-button>
          <div class="sp-products-list">
            <div
                @contextmenu="ctxMenu($event, product)"
                v-for="(product, index) in $sp.data.productsBulk.children"
                class="sp-products-list-product">
              <grid-product
                  :currencies="$sp.data.currencies"
                  :product="product">
              </grid-product>
              <el-button
                  class="sp-delete-btn"
                  @click="$sp.data.productsBulk.children.splice(index, 1)"
                  size="mini"
                  type="primary"
                  icon="el-icon-minus">
              </el-button>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
    <context-menu
      class="sp-context-menu"
      ref="ctx"
      @ctx-open="ctxOpen">
      <li @click="data.modal = menu.product; data.modalVisible = true">{{$sp.print['Open']}}</li>
      <li @click="viewUrl(menu.product.permalink.value)">{{$sp.print['View']}}</li>
      <li @click="advanced(menu.product.id.value)">{{$sp.print['Advanced']}}</li>
    </context-menu>
    <el-dialog
        :title="$sp.print['DeletePermanently']"
        :visible.sync="deleteForce">
      <el-form>
        <span class="sp-delete-warning">{{$sp.print['ThisCanNotBeUndone']}}</span>
        <el-button @click="deleteForce = false">{{$sp.print['Cacel']}}</el-button>
        <el-button style="float: right" autofocus @click="doDelete()">{{$sp.print['Delete']}}</el-button>
      </el-form>
    </el-dialog>
  </el-col>
</template>

<script>
  import gridProduct from '../../../../components/Product'
  import PropGallery from '../../../../components/properties/PropGallery'
  import PropCategories from '../../../../components/properties/PropCategories'
  import PropTags from '../../../../components/properties/PropTags'
  import PropTextarea from '../../../../components/properties/PropTextarea'
  import PropProducts from '../../../../components/properties/PropProducts'
  import PropProductAttributes from '../../../../components/properties/PropProductAttributes/PropProductAttributes'
  import PropSwitch from '../../../../components/properties/PropSwitch'
  import PropNumber from '../../../../components/properties/PropNumber'
  import PropDownloads from '../../../../components/properties/PropDownloads.vue'
  export default {
    name: 'products',
    props: [
      'data',
      'browser'
    ],
    components: {
      PropDownloads,
      PropNumber,
      PropSwitch,
      PropTextarea,
      PropGallery,
      PropCategories,
      PropTags,
      PropProducts,
      PropProductAttributes,
      gridProduct
    },
    data () {
      return {
        menu: null,
        deleteForce: false,
        loading: false,
        view: 'menu',
        title: new this.$pap.Prop(null, 'title', ''),
        slug: new this.$pap.Prop(null, 'slug', ''),
        gallery: new this.$pap.Prop(null, 'images', []),
        categories: new this.$pap.Prop(null, 'categories', []),
        tags: new this.$pap.Prop(null, 'tags', []),
        description: new this.$pap.Prop(null, 'description', ''),
        shortDescription: new this.$pap.Prop(null, 'short_description', ''),
        purchaseNote: new this.$pap.Prop(null, 'purchase_note', ''),
        upsells: new this.$pap.Prop(null, 'upsells', []),
        crossSells: new this.$pap.Prop(null, 'cross_sells', []),
        attributes: new this.$pap.Prop(null, 'attributes', []),
        featured: new this.$pap.Prop(null, 'featured', false),
        virtual: new this.$pap.Prop(null, 'virtual', false),
        manageStock: new this.$pap.Prop(null, 'manage_stock', false),
        stockQuantity: new this.$pap.Prop(null, 'stock_quantity', 0),
        allowBackorders: new this.$pap.Prop(null, 'allow_backorders', 'no'), // no, notify, yes
        soldIndividually: new this.$pap.Prop(null, 'sold_individually', false),
        downloadable: new this.$pap.Prop(null, 'downloadable', false),
        downloads: new this.$pap.Prop(null, 'downloads', []),
        downloadLimit: new this.$pap.Prop(null, 'download_limit', -1),
        downloadExpiry: new this.$pap.Prop(null, 'download_expiry', -1),
        weight: new this.$pap.Prop(null, 'weight'),
        width: new this.$pap.Prop(null, 'width'),
        height: new this.$pap.Prop(null, 'height'),
        length: new this.$pap.Prop(null, 'length'),
        regularPrice: new this.$pap.Prop(null, 'regular_price', 0),
        regularPriceBy: 'fixed',
        salePrice: new this.$pap.Prop(null, 'sale_price', 0),
        salePriceBy: 'fixed',
        editors: {
          status: this.$sp.print['Status'],
          title: this.$sp.print['TitleAndSlug'],
          gallery: this.$sp.print['Gallery'],
          price: this.$sp.print['PriceAndSale'],
          categories: this.$sp.print['Categories'],
          tags: this.$sp.print['Tags'],
          description: this.$sp.print['Description'],
          purchaseNote: this.$sp.print['PurchaseNote'],
          upsells: this.$sp.print['Upsells'],
          crossSells: this.$sp.print['CrossSells'],
          attributes: this.$sp.print['Attributes'],
          stock: this.$sp.print['Inventory'],
          downloads: this.$sp.print['Downloads'],
          shipping: this.$sp.print['Shipping']
        }
      }
    },
    watch: {
      'changes' (changes) {
        this.$sp.data.changes = changes
      },
      '$sp.data.on.save' () {
        this.doSave()
      }
    },
    computed: {
      changes () {
        let changes = 0
        this.$sp.data.productsBulk.children.forEach(child => {
          changes += child.changes(false, true).length
        })
        return changes
      }
    },
    methods: {
      ctxMenu (e, prod) {
        e.preventDefault()
        this.$refs.ctx.open(e, {product: prod})
      },
      viewUrl (url) {
        window.open(url, '_blank')
      },
      advanced (id) {
        let url = this.$sp.server
        url += '/wp-admin/post.php?post=' + id + '&action=edit'
        this.viewUrl(url)
      },
      ctxOpen (data) {
        this.menu = data
      },
      doDelete () {
        this.loading = true
        let i = this.$sp.data.productsBulk.children.length
        this.$sp.data.productsBulk.children.forEach(child => {
          child.remove(true).then(() => {
            i--
            if (i === 0) {
              this.browser.getProducts()
              this.$sp.data.productsBulk.children = []
              this.loading = false
            }
          })
        })
        this.deleteForce = false
      },
      doTrash () {
        this.loading = true
        let i = this.$sp.data.productsBulk.children.length
        this.$sp.data.productsBulk.children.forEach(child => {
          child.remove().then(() => {
            i--
            if (i === 0) {
              this.loading = false
              this.$message({
                type: 'success',
                message: 'Trashed'
              })
            }
          })
        })
      },
      doPublish () {
        this.$sp.data.productsBulk.children.forEach(child => {
          child.status.value = 'publish'
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Publish set'
          })
        }
      },
      doDraft () {
        this.$sp.data.productsBulk.children.forEach(child => {
          child.status.value = 'draft'
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Draft set'
          })
        }
      },
      doPending () {
        this.$sp.data.productsBulk.children.forEach(child => {
          child.status.value = 'pending'
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Pending set'
          })
        }
      },
      doPrivate () {
        this.$sp.data.productsBulk.children.forEach(child => {
          child.status.value = 'private'
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Private set'
          })
        }
      },
      doReplace (prop, value) {
        this.$sp.data.productsBulk.children.forEach(child => {
          if (prop === 'width' || prop === 'height' || prop === 'length') {
            child.dimensions.value[prop] = value
          } else {
            child[prop].value = value
          }
          if (prop === 'images') {
            let i = 0
            child[prop].value.forEach(val => {
              val.position = i
              i++
            })
          }
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Replaced'
          })
        }
      },
      doAppend (prop, value) {
        this.$sp.data.productsBulk.children.forEach(child => {
          child[prop].value = child[prop].value + value
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Appended'
          })
        }
      },
      doPrepend (prop, value) {
        this.$sp.data.productsBulk.children.forEach(child => {
          child[prop].value = value + child[prop].value
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Prepended'
          })
        }
      },
      doIncrease (prop, value, percentage = false) {
        this.$sp.data.productsBulk.children.forEach(child => {
          if (percentage) {
            if (prop === 'width' || prop === 'height' || prop === 'length') {
              child.dimensions.value[prop] = (Number(child[prop].value) * (1 + (Number(value) / 100)))
            } else {
              child[prop].value = (Number(child[prop].value) * (1 + (Number(value) / 100)))
            }
          } else {
            if (prop === 'width' || prop === 'height' || prop === 'length') {
              child.dimensions.value[prop] = (Number(child[prop].value) + Number(value))
            } else {
              child[prop].value = (Number(child[prop].value) + Number(value))
            }
          }
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Increased'
          })
        }
      },
      doDecrease (prop, value, percentage = false) {
        this.$sp.data.productsBulk.children.forEach(child => {
          if (percentage) {
            if (prop === 'width' || prop === 'height' || prop === 'length') {
              child.dimensions.value[prop] = (Number(child[prop].value) * (Number(value) / 100))
            } else {
              child[prop].value = (Number(child[prop].value) * (Number(value) / 100))
            }
          } else {
            if (prop === 'width' || prop === 'height' || prop === 'length') {
              child.dimensions.value[prop] = (Number(child[prop].value) - Number(value))
            } else {
              child[prop].value = (Number(child[prop].value) - Number(value))
            }
          }
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Decreased'
          })
        }
      },
      doMerge (prop, value) {
        if (value === null || value.constructor !== Array) {
          value = []
        }
        this.$sp.data.productsBulk.children.forEach(child => {
          if (child[prop].value === null || child[prop].value.constructor !== Array) {
            child[prop].value = []
          }
          value.forEach(val => {
            child[prop].value.push(val)
          })
          if (prop === 'images') {
            let i = 0
            child[prop].value.forEach(val => {
              val.position = i
              i++
            })
          }
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Appended'
          })
        }
      },
      doPreMerge (prop, value) {
        if (value === null || value.constructor !== Array) {
          value = []
        }
        this.$sp.data.productsBulk.children.forEach(child => {
          if (child[prop].value === null || child[prop].value.constructor !== Array) {
            child[prop].value = []
          }
          value.reverse().forEach(val => {
            child[prop].value.unshift(val)
          })
          if (prop === 'images') {
            let i = 0
            child[prop].value.forEach(val => {
              val.position = i
              i++
            })
          }
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Prepended'
          })
        }
      },
      doRemove (prop, value) {
        this.$sp.data.productsBulk.children.forEach(child => {
          let i = 0
          child[prop].value.forEach(obj => {
            value.forEach(val => {
              if (typeof obj.id !== 'undefined' && typeof val.id !== 'undefined' && obj.id === val.id) {
                child[prop].value.splice(i, 1)
              }
            })
            i++
          })
        })
        if (this.$sp.data.autosave) {
          this.doSave()
        } else {
          this.$message({
            type: 'success',
            message: 'Removed'
          })
        }
      },
      doSave () {
        this.loading = true
        let i = this.$sp.data.productsBulk.children.length
        this.$sp.data.productsBulk.children.forEach(child => {
          child.save().then(() => {
            i--
            if (i === 0) {
              this.loading = false
            }
          }).catch(e => {
            i--
            if (i === 0) {
              this.loading = false
            }
          })
        })
      }
    }
  }
</script>

<style>
  .actions li {
    float: left;
    margin-right: 20px;
    font-size: 13px;
  }
  .actions li, .actions li a {
    color: #20a0ff;
    cursor: pointer;
    text-decoration: none;
  }
  .actions li:hover, .actions li a:hover {
    color: rgba(32, 157, 255, 0.64);
  }
  .actions {
    padding: 0;
    display: inline-block;
    list-style-type: none;
    margin: 0;
  }
  .sp-products-list {
    display: inline-block;
    padding: 10px 0 0 10px;
    margin-left: -10px;
  }
  .sp-products-list div {
    position: relative;
  }
  .sp-products-list .sp-delete-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 1000;
    opacity: 0;
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
</style>
