import Endpoint from 'papir/src/logic/form/endpoint'
import Prop from 'papir/src/logic/form/prop'

/**
 * Settings
 */
export default class Settings extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('settings', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
    this.id = new Prop(this, 'id', id)
    this.label = new Prop(this, 'label')
    this.description = new Prop(this, 'description')
    this.value = new Prop(this, 'value')
    this.default = new Prop(this, 'default')
    this.tip = new Prop(this, 'tip')
    this.placeholder = new Prop(this, 'placeholder')
    this.type = new Prop(this, 'type')
    this.options = new Prop(this, 'options')
  }
}
