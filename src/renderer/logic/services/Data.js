import Vue from 'vue'
import { List, Endpoint } from 'papir'
export default class Data {
  constructor (models, controller, server, dev) {
    let vm = new Vue({
      data: {
        loaded: false,
        preload: null,
        license: new Endpoint('license', controller, 'sp'),
        products: new List(models.Product, controller),
        productsBulk: new List(models.Product, controller),
        productAttribute: new models.ProductAttribute(controller),
        productAttributes: new List(models.ProductAttribute, controller),
        productAttributeTerms: new List(models.ProductAttributeTerm, controller),
        categories: new List(models.ProductCategory, controller),
        tags: new List(models.ProductTag, controller),
        taxClasses: new List(models.TaxClass, controller),
        shippingClasses: new List(models.ProductShippingClass, controller),
        currencies: new Endpoint('settingsGeneral', controller),
        categoriesNested: [],
        settings: {
          shop_page_display: null,
          category_archive_display: null,
          default_catalog_orderby: null,
          shop_catalog_image_size: null,
          shop_single_image_size: null,
          shop_thumbnail_image_size: null,
          weight_unit: null,
          dimension_unit: null,
          enable_reviews: null,
          review_rating_verification_label: null,
          review_rating_verification_required: null,
          enable_review_rating: null,
          review_rating_required: null,
          cart_redirect_after_add: null,
          enable_ajax_add_to_cart: null,
          manage_stock: null,
          hold_stock_minutes: null,
          notify_low_stock: null,
          notify_no_stock: null,
          stock_email_recipient: null,
          notify_low_stock_amount: null,
          notify_no_stock_amount: null,
          hide_out_of_stock_items: null,
          stock_format: null,
          file_download_method: null,
          downloads_require_login: null,
          downloads_grant_access_after_payment: null
        },
        exec: {
          save: () => {
            vm.on.save = !vm.on.save
          },
          refresh: () => {
            vm.on.refresh = !vm.on.refresh
          }
        },
        on: {
          save: false,
          refresh: false
        },
        changes: 0,
        autosave: false,
        storage: {
          browser: {
            page: 1,
            perPage: 12,
            perRow: 3,
            display: [ // Default props in table view
              'Name',
              'Slug',
              'Price',
              'Sale',
              'Status'
            ],
            view: 'table' // table or grid
          }
        }
      },
      created () {
        let storage = localStorage.getItem('sp-local-storage')
        if (storage !== null) {
          this.storage = JSON.parse(storage)
        }
      },
      watch: {
        storage: {
          handler (value) {
            localStorage.setItem('sp-local-storage', JSON.stringify(value))
          },
          deep: true
        }
      }
    })
    this.vm = vm
    this.preload = (data = null) => {
      /* eslint-disable */
      if (typeof preload !== 'undefined') {
        data = preload
      }
      /* eslint-enable */
      if (data !== null) {
        data = JSON.parse(data)
      }
      this.vm.preload = data
      this.vm.loaded = true
    }
    if (self !== top && dev) {
      let receivePreload = (event) => {
        let json
        try {
          json = JSON.parse(event.data)
        } catch (e) {
          json = null
        }
        if (json !== null && json.type === 'preload') {
          this.preload(json.data)
        }
      }
      window.addEventListener('message', receivePreload, false)
      parent.window.postMessage(JSON.stringify({
        type: 'sp_preload'
      }), server)
    } else {
      this.preload()
    }
  }
}
