import Endpoint from 'papir/src/logic/form/endpoint'
// import Prop from 'papir/src/logic/form/prop'

/**
 * Settings
 */
export default class SettingsProduct extends Endpoint {
  constructor (controller, id = null, apiSlug = controller.default, predefined = {}) {
    /**
     * Pass to Endpoint model
     */
    super('settingsProduct', controller, apiSlug, predefined)

    /**
     * Define properties
     * @note Props fetched which is not defined is available, but is not recognized in code editors
     */
  }
}
