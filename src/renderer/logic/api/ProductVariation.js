import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * ProductVariation
 */
export default class ProductVariation extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productVariation', controller, apiSlug, predefined)

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
      let img = {}
      if (prop.value !== null && prop.value.constructor === Object) {
        let p = prop.value
        if (typeof p.id !== 'undefined') { img.id = p.id }
        if (typeof p.name !== 'undefined') { img.name = p.name }
        if (typeof p.position !== 'undefined') { img.position = p.position }
        if (typeof p.alt !== 'undefined') { img.alt = p.alt }
        if (typeof p.src !== 'undefined') { img.src = p.src }
      } else {
        img = prop.value
      }
      return img
    }

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.parent_id = new Prop(this, 'parent_id') // This property is handled in URL
    this.id = new Prop(this, 'id', id)
    this.date_created = new Prop(this, 'date_created')
    this.date_modified = new Prop(this, 'date_modified')
    this.description = new Prop(this, 'description')
    this.description_raw = new Prop(this, 'description_raw')
    this.permalink = new Prop(this, 'permalink')
    this.sku = new Prop(this, 'sku')
    this.price = new Prop(this, 'price')
    this.regular_price = new Prop(this, 'regular_price')
    this.sale_price = new Prop(this, 'sale_price')
    this.date_on_sale_from = new Prop(this, 'date_on_sale_from')
    this.date_on_sale_from_gmt = new Prop(this, 'date_on_sale_from_gmt')
    this.date_on_sale_to = new Prop(this, 'date_on_sale_to')
    this.date_on_sale_to_gmt = new Prop(this, 'date_on_sale_to_gmt')
    this.on_sale = new Prop(this, 'on_sale')
    this.visible = new Prop(this, 'visible')
    this.purchasable = new Prop(this, 'purchasable')
    this.virtual = new Prop(this, 'virtual')
    this.downloadable = new Prop(this, 'downloadable')
    this.downloads = new Prop(this, 'downloads', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.download_limit = new Prop(this, 'download_limit')
    this.download_expiry = new Prop(this, 'download_expiry')
    this.tax_status = new Prop(this, 'tax_status')
    this.tax_class = new Prop(this, 'tax_class')
    this.manage_stock = new Prop(this, 'manage_stock')
    this.stock_quantity = new Prop(this, 'stock_quantity')
    this.in_stock = new Prop(this, 'in_stock')
    this.backorders = new Prop(this, 'backorders')
    this.backorders_allowed = new Prop(this, 'backorders_allowed')
    this.backordered = new Prop(this, 'backordered')
    this.weight = new Prop(this, 'weight')
    this.dimensions = new Prop(this, 'dimensions')
    this.shipping_class = new Prop(this, 'shipping_class')
    this.shipping_class_id = new Prop(this, 'shipping_class_id')
    this.image = new Prop(this, 'image', null, {}, (accessor) => { return imageTranspiler(accessor) })
    this.attributes = new Prop(this, 'attributes', null, {}, (accessor) => { return emptyArrayTranspiler(accessor) })
    this.menu_order = new Prop(this, 'menu_order')
    this.meta_data = new Prop(this, 'meta_data')
  }
}
