import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * TaxClass
 */
export default class TaxClass extends Endpoint {
  constructor (controller, slug = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('taxClass', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.slug = new Prop(this, 'slug', slug)
    this.name = new Prop(this, 'name')
  }
}
