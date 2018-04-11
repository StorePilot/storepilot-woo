import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * Product
 */
export default class Product extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('product', controller, apiSlug, predefined)

    let emptyArrayTranspiler = (prop) => {
      if (
        prop.parent.id.value !== null &&
        (prop.value === null ||
        (prop.value.constructor === Array && prop.value.length === 0))) {
        return [{ id: 0 }] // @todo - Strange behaviour, post issue on woocommerce? This should work with [] instead..
      } else {
        return prop.value
      }
    }

    let imageTranspiler = (prop) => {
      let val = []
      if (prop.value !== null && prop.value.constructor === Array) {
        prop.value.forEach(p => {
          let img = {}
          if (typeof p.id !== 'undefined') { img.id = p.id }
          if (typeof p.name !== 'undefined') { img.name = p.name }
          if (typeof p.position !== 'undefined') { img.position = p.position }
          if (typeof p.alt !== 'undefined') { img.alt = p.alt }
          if (typeof p.src !== 'undefined') { img.src = p.src }
          val.push(img)
        })
      } else {
        val = prop.value
      }
      return val
    }

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.permalink = new Prop(this, 'permalink')
    this.date_created = new Prop(this, 'date_created')
    this.date_created_gmt = new Prop(this, 'date_created_gmt')
    this.date_modified_gmt = new Prop(this, 'date_modified_gmt')
    this.date_modified = new Prop(this, 'date_modified')
    this.price = new Prop(this, 'price')
    this.price_html = new Prop(this, 'price_html')
    this.price_range = new Prop(this, 'price_range')
    this.on_sale = new Prop(this, 'on_sale')
    this.purchasable = new Prop(this, 'purchasable')
    this.total_sales = new Prop(this, 'total_sales')
    this.backorders_allowed = new Prop(this, 'backorders_allowed')
    this.backordered = new Prop(this, 'backordered')
    this.shipping_required = new Prop(this, 'shipping_required')
    this.shipping_taxable = new Prop(this, 'shipping_taxable')
    this.shipping_class_id = new Prop(this, 'shipping_class_id')
    this.average_rating = new Prop(this, 'average_rating')
    this.rating_count = new Prop(this, 'rating_count')
    this.related_ids = new Prop(this, 'related_ids')
    this.variations = new Prop(this, 'variations')
    this.grouped_products = new Prop(this, 'grouped_products')
    this.name = new Prop(this, 'name')
    this.slug = new Prop(this, 'slug')
    this.type = new Prop(this, 'type')
    this.status = new Prop(this, 'status')
    this.featured = new Prop(this, 'featured')
    this.catalog_visibility = new Prop(this, 'catalog_visibility')
    this.description = new Prop(this, 'description')
    this.description_raw = new Prop(this, 'description_raw')
    this.short_description = new Prop(this, 'short_description')
    this.short_description_raw = new Prop(this, 'short_description_raw')
    this.sku = new Prop(this, 'sku')
    this.regular_price = new Prop(this, 'regular_price')
    this.sale_price = new Prop(this, 'sale_price')
    this.date_on_sale_from = new Prop(this, 'date_on_sale_from')
    this.date_on_sale_from_gmt = new Prop(this, 'date_on_sale_from_gmt')
    this.date_on_sale_to = new Prop(this, 'date_on_sale_to')
    this.date_on_sale_to_gmt = new Prop(this, 'date_on_sale_to_gmt')
    this.virtual = new Prop(this, 'virtual')
    this.downloadable = new Prop(this, 'downloadable')
    this.downloads = new Prop(this, 'downloads', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.download_limit = new Prop(this, 'download_limit')
    this.download_expiry = new Prop(this, 'download_expiry')
    this.external_url = new Prop(this, 'external_url')
    this.button_text = new Prop(this, 'button_text')
    this.tax_status = new Prop(this, 'tax_status')
    this.tax_class = new Prop(this, 'tax_class')
    this.manage_stock = new Prop(this, 'manage_stock')
    this.stock_quantity = new Prop(this, 'stock_quantity')
    this.in_stock = new Prop(this, 'in_stock')
    this.backorders = new Prop(this, 'backorders')
    this.sold_individually = new Prop(this, 'sold_individually')
    this.weight = new Prop(this, 'weight')
    this.dimensions = new Prop(this, 'dimensions')
    this.shipping_class = new Prop(this, 'shipping_class')
    this.reviews_allowed = new Prop(this, 'reviews_allowed')
    this.upsell_ids = new Prop(this, 'upsell_ids')
    this.upsell = new Prop(this, 'upsell')
    this.cross_sell_ids = new Prop(this, 'cross_sell_ids')
    this.cross_sell = new Prop(this, 'cross_sell')
    this.parent_id = new Prop(this, 'parent_id')
    this.purchase_note = new Prop(this, 'purchase_note')
    this.purchase_note_raw = new Prop(this, 'purchase_note_raw')
    this.categories = new Prop(this, 'categories', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.tags = new Prop(this, 'tags', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.images = new Prop(this, 'images', null, {}, (accessor) => { return imageTranspiler(accessor) })
    this.attributes = new Prop(this, 'attributes', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.default_attributes = new Prop(this, 'default_attributes', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.menu_order = new Prop(this, 'menu_order')
    this.meta_data = new Prop(this, 'meta_data')
  }
}
