<template>
  <el-col class="sp-editor-wrapper">

    <span class="sp-editor-id">
      {{$sp.print['WoocommerceSettings']}}
    </span>

    <div style="display: inline-block; width: 100%">
      <div class="sp-sub-header">
        <div class="sp-title-wrapper">
          <span class="sp-editor-title">
            {{$sp.print['ShopConfiguration']}}
          </span>
          <ul class="actions" style="width: calc(100% - 74px - 20px);">
            <li>
              <a :href="$sp.server + '/wp-admin/admin.php?page=wc-settings&tab=products&section'" target="_blank">
                {{$sp.print['Advanced']}}
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="sp-content-padding">
        <table
            cellspacing="0"
            style="width: 100%; border-collapse: collapse"
            class="sp-options">
          <tbody style="width: 100%;">
            <h3>{{$sp.print['Display']}}</h3>
            <tr>
              <td>{{$sp.print['CatalogOrderBy']}}</td>
              <td>
                <el-select
                    v-if="$sp.data.settings !== null && $sp.data.settings.default_catalog_orderby !== null"
                    v-loading="$sp.data.settings.default_catalog_orderby.value.loading || $sp.data.settings.default_catalog_orderby.loading"
                    v-model="$sp.data.settings.default_catalog_orderby.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.default_catalog_orderby.value.save() : ''"
                    :placeholder="$sp.print['OrderBy']">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.default_catalog_orderby.invalids.options.value)"
                      :label="removeAmp($sp.data.settings.default_catalog_orderby.invalids.options.value[option])"
                      :value="option">
                  </el-option>
                </el-select>
              </td>
            </tr>
            <tr>
              <td>{{$sp.print['ShopPageDisplay']}}</td>
              <td>
                <el-select
                    v-loading="$sp.data.settings.shop_page_display.value.loading || $sp.data.settings.shop_page_display.loading"
                    v-model="$sp.data.settings.shop_page_display.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.shop_page_display.value.save() : ''"
                    :placeholder="$sp.print['Products']">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.shop_page_display.invalids.options.value)"
                      :label="removeAmp($sp.data.settings.shop_page_display.invalids.options.value[option])"
                      :value='option'>
                  </el-option>
                </el-select>
              </td>
            </tr>
            <tr>
              <td>{{$sp.print['CategoryArchiveDisplay']}}</td>
              <td>
                <el-select
                    v-loading="$sp.data.settings.category_archive_display.value.loading || $sp.data.settings.category_archive_display.loading"
                    v-model="$sp.data.settings.category_archive_display.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.category_archive_display.value.save() : ''"
                    :placeholder="$sp.print['Products']">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.category_archive_display.invalids.options.value)"
                      :label="removeAmp($sp.data.settings.category_archive_display.invalids.options.value[option])"
                      :value='option'>
                  </el-option>
                </el-select>
              </td>
            </tr>
            <!--h3>Images</h3>
            <tr>
              <td>Shop Catalog Image Size</td>
              <td>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_catalog_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_catalog_image_size.value.value.width">
                  <template slot="prepend">Width&nbsp;</template>
                  <template slot="append">px</template>
                </el-input>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_catalog_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_catalog_image_size.value.value.height">
                  <template slot="prepend">Height</template>
                  <template slot="append">px</template>
                </el-input>
                <i style="font-size: .8em">Crop</i>
                <el-switch
                    disabled
                    v-loading="$sp.data.settings.shop_catalog_image_size.loading"
                    v-model="$sp.data.settings.shop_catalog_image_size.value.value.crop"
                    active-color="#13ce66"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Shop Single Image Size</td>
              <td>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_single_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_single_image_size.value.value.width">
                  <template slot="prepend">Width&nbsp;</template>
                  <template slot="append">px</template>
                </el-input>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_single_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_single_image_size.value.value.height">
                  <template slot="prepend">Height</template>
                  <template slot="append">px</template>
                </el-input>
                <i style="font-size: .8em">Crop</i>
                <el-switch
                    disabled
                    v-loading="$sp.data.settings.shop_single_image_size.loading"
                    v-model="$sp.data.settings.shop_single_image_size.value.value.crop"
                    active-color="#13ce66"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Shop Thumbnail Size</td>
              <td>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_thumbnail_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_thumbnail_image_size.value.value.width">
                  <template slot="prepend">Width&nbsp;</template>
                  <template slot="append">px</template>
                </el-input>
                <el-input
                    disabled
                    style="margin-bottom: 10px"
                    @blur="$sp.data.autosave ? $sp.data.settings.shop_thumbnail_image_size.value.save() : ''"
                    v-model="$sp.data.settings.shop_thumbnail_image_size.value.value.height">
                  <template slot="prepend">Height</template>
                  <template slot="append">px</template>
                </el-input>
                <i style="font-size: .8em">Crop</i>
                <el-switch
                    disabled
                    v-loading="$sp.data.settings.shop_thumbnail_image_size.loading"
                    v-model="$sp.data.settings.shop_thumbnail_image_size.value.value.crop"
                    active-color="#13ce66"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <h3>Units</h3>
            <tr>
              <td>Weight Unit</td>
              <td>
                <el-select
                    disabled
                    v-if="typeof $sp.data.settings.weight_unit.options.value !== 'undefined'"
                    v-loading="$sp.data.settings.weight_unit.loading"
                    v-model="$sp.data.settings.weight_unit.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.weight_unit.value.save() : ''"
                    placeholder="Unit">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.weight_unit.options.value)"
                      :label="removeAmp($sp.data.settings.weight_unit.options.value[option])"
                      :value="String(option)">
                  </el-option>
                </el-select>
              </td>
            </tr>
            <tr>
              <td>Dimension Unit</td>
              <td>
                <el-select
                    disabled
                    v-if="typeof $sp.data.settings.dimension_unit.options.value !== 'undefined'"
                    v-loading="$sp.data.settings.dimension_unit.loading"
                    v-model="$sp.data.settings.dimension_unit.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.dimension_unit.value.save() : ''"
                    placeholder="Unit">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.dimension_unit.options.value)"
                      :label="removeAmp($sp.data.settings.dimension_unit.options.value[option])"
                      :value="option">
                  </el-option>
                </el-select>
              </td>
            </tr>
            <h3>Inventory</h3>
            <tr>
              <td>Stock Email Recipient</td>
              <td>
                <el-input
                    @change="$sp.data.autosave ? $sp.data.settings.stock_email_recipient.value.save() : ''"
                    v-model="$sp.data.settings.stock_email_recipient.value.value">
                  <template slot="prepend">Email</template>
                </el-input>
              </td>
            </tr>
            <tr>
              <td>Manage Stock</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.manage_stock.loading"
                    v-model="$sp.data.settings.manage_stock.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Hold Stock Minutes</td>
              <td>
                <el-input
                    v-loading="$sp.data.settings.hold_stock_minutes.loading"
                    v-model="$sp.data.settings.hold_stock_minutes.value.value"
                    @blur="$sp.data.autosave ? $sp.data.settings.hold_stock_minutes.value.save() : ''">
                  <template slot="append">Minutes</template>
                </el-input>
              </td>
            </tr>
            <tr>
              <td>Notify Low Stock</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.notify_low_stock.loading"
                    v-model="$sp.data.settings.notify_low_stock.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Notify No Stock</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.notify_no_stock.loading"
                    v-model="$sp.data.settings.notify_no_stock.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Notify Low Stock Amount</td>
              <td>
                <el-input
                    v-loading="$sp.data.settings.notify_low_stock_amount.loading"
                    v-model="$sp.data.settings.notify_low_stock_amount.value.value"
                    @blur="$sp.data.autosave ? $sp.data.settings.notify_low_stock_amount.value.save() : ''">
                  <template slot="prepend">Quantity</template>
                  <template slot="append">Pieces</template>
                </el-input>
              </td>
            </tr>
            <tr>
              <td>Notify No Stock Amount</td>
              <td>
                <el-input
                    v-loading="$sp.data.settings.notify_no_stock_amount.loading"
                    v-model="$sp.data.settings.notify_no_stock_amount.value.value"
                    @blur="$sp.data.autosave ? $sp.data.settings.notify_no_stock_amount.value.save() : ''">
                  <template slot="prepend">Quantity</template>
                  <template slot="append">Pieces</template>
                </el-input>
              </td>
            </tr>
            <tr>
              <td>Hide Out Of Stock Items</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.hide_out_of_stock_items.loading"
                    v-model="$sp.data.settings.hide_out_of_stock_items.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Stock Format</td>
              <td>
                <el-select
                    v-if="typeof $sp.data.settings.stock_format.options.value !== 'undefined'"
                    v-loading="$sp.data.settings.stock_format.loading"
                    v-model="$sp.data.settings.stock_format.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.stock_format.value.save() : ''"
                    placeholder="Format">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.stock_format.options.value)"
                      :label="removeAmp($sp.data.settings.stock_format.options.value[option])"
                      :value="option">
                  </el-option>
                </el-select>
              </td>
            </tr>
            <h3>Reviews</h3>
            <tr>
              <td>Enable Reviews</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.enable_reviews.loading"
                    v-model="$sp.data.settings.enable_reviews.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Review Rating Verification Label</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.review_rating_verification_label.loading"
                    v-model="$sp.data.settings.review_rating_verification_label.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Review Rating Verification Label Required</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.review_rating_verification_required.loading"
                    v-model="$sp.data.settings.review_rating_verification_required.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Enable Review Rating</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.enable_review_rating.loading"
                    v-model="$sp.data.settings.enable_review_rating.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Review Rating Required</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.review_rating_required.loading"
                    v-model="$sp.data.settings.review_rating_required.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <h3>Cart</h3>
            <tr>
              <td>Cart Redirect After Add</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.cart_redirect_after_add.loading"
                    v-model="$sp.data.settings.cart_redirect_after_add.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Enable Ajax Add To Cart</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.enable_ajax_add_to_cart.loading"
                    v-model="$sp.data.settings.enable_ajax_add_to_cart.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <h3>Downloadables</h3>
            <tr>
              <td>File Download Method</td>
              <td>
                <el-select
                    disabled
                    v-if="typeof $sp.data.settings.file_download_method.options.value !== 'undefined'"
                    v-loading="$sp.data.settings.file_download_method.loading"
                    v-model="$sp.data.settings.file_download_method.value.value"
                    @change="$sp.data.autosave ? $sp.data.settings.file_download_method.value.save() : ''"
                    placeholder="Method">
                  <el-option
                      :key="option"
                      v-for="option in Object.keys($sp.data.settings.file_download_method.options.value)"
                      :label="removeAmp($sp.data.settings.file_download_method.options.value[option])"
                      :value="option">
                  </el-option>
                </el-select>
              </td>
            </tr>
            <tr>
              <td>Downloads Require Login</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.downloads_require_login.loading"
                    v-model="$sp.data.settings.downloads_require_login.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr>
            <tr>
              <td>Downloads Grant Access After Payment</td>
              <td>
                <el-switch
                    v-loading="$sp.data.settings.downloads_grant_access_after_payment.loading"
                    v-model="$sp.data.settings.downloads_grant_access_after_payment.value.value"
                    active-color="#13ce66"
                    on-value="yes"
                    off-value="no"
                    active-text=""
                    inactive-text="">
                </el-switch>
              </td>
            </tr-->
          </tbody>
        </table>
      </div>
    </div>
  </el-col>
</template>

<script>
  import propMedia from '../../../../components/properties/PropMedia'
  import propTextarea from '../../../../components/properties/PropTextarea'
  import propImage from '../../../../components/properties/PropImage'
  import propTitle from '../../../../components/properties/PropTitle'
  export default {
    name: 'Settings',
    props: [
      'data'
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
    watch: {
      '$sp.data.on.save' () {
        Object.keys(this.$sp.data.settings).forEach(setting => {
          if (this.$sp.data.settings[setting].changes(false, true).length > 0) {
            console.log(9)
            this.$sp.data.settings[setting].save()
          }
        })
      },
      '$sp.data.settings': {
        handler () {
          this.$sp.data.changes = this.detectChanges()
        },
        deep: true
      }
    },
    methods: {
      detectChanges () {
        let settingsChanged = 0
        Object.keys(this.$sp.data.settings).forEach(setting => {
          settingsChanged += this.$sp.data.settings[setting].changes(false, true).length
        })
        return settingsChanged
      },
      removeAmp (val) {
        val = String(val).replace('&amp;', '&')
        return val
      }
    }
  }
</script>

<style>
  .sp-editor-id {
    position: absolute!important;
    right: 20px;
  }
</style>
