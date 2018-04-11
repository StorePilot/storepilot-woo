import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * ProductShippingClass
 */
export default class ProductShippingClass extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productShippingClass', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.name = new Prop(this, 'name')
    this.slug = new Prop(this, 'slug')
    this.count = new Prop(this, 'count')
  }
}
