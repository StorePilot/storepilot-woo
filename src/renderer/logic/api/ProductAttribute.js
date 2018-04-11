import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * Settings
 */
export default class ProductAttribute extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productAttribute', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.name = new Prop(this, 'name')
    this.type = new Prop(this, 'type')
    this.slug = new Prop(this, 'slug')
    this.order_by = new Prop(this, 'order_by')
    this.has_archives = new Prop(this, 'has_archives')
  }
}
