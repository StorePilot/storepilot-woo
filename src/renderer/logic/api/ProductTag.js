import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * ProductTag
 */
export default class ProductTag extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('productTag', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.name = new Prop(this, 'name')
    this.slug = new Prop(this, 'slug')
    this.description = new Prop(this, 'description')
    this.count = new Prop(this, 'count')
    this.permalink = new Prop(this, 'permalink')
  }
}
