import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * Settings
 */
export default class ProductAttributeTerm extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productAttributeTerm', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.parent_id = new Prop(this, 'parent_id') // This property is handled in URL
    this.id = new Prop(this, 'id', id)
    this.name = new Prop(this, 'name')
    this.slug = new Prop(this, 'slug')
    this.description = new Prop(this, 'description')
    this.menu_order = new Prop(this, 'menu_order')
    this.count = new Prop(this, 'count')
  }
}
