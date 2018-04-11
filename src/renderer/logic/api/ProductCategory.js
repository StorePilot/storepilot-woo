import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * ProductCategory
 */
export default class ProductCategory extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productCategory', controller, apiSlug, predefined)

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
    this.id = new Prop(this, 'id', id)
    this.name = new Prop(this, 'name')
    this.slug = new Prop(this, 'slug')
    this.description = new Prop(this, 'description')
    this.description_raw = new Prop(this, 'description_raw')
    this.display = new Prop(this, 'display')
    this.image = new Prop(this, 'image', null, {}, (accessor) => { return imageTranspiler(accessor) })
    this.menu_order = new Prop(this, 'menu_order')
    this.parent = new Prop(this, 'parent')
    this.count = new Prop(this, 'count')
    this.permalink = new Prop(this, 'permalink')
    this.header = new Prop(this, 'header')
    this.footer = new Prop(this, 'footer')
    this.header_raw = new Prop(this, 'header_raw')
    this.footer_raw = new Prop(this, 'footer_raw')
  }
}
