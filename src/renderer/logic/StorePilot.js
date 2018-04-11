/**
 * This file serves all shared logic behind StorePilot frontend
 */
import * as importer from './services/Importer'
let locale = 'enUS'
class StorePilot {
  constructor () {
    this.install = (Vue, options) => {
      Object.assign({
        controller: null,
        dev: false
      }, options)
      if (typeof options.server === 'undefined') {
        options.server = this.getServer(options.dev)
      }
      let data = new importer.Data(new importer.Modeler(), options.controller, options.server, options.dev)
      Vue.prototype.$sp = {
        dev: options.dev,
        server: options.server,
        data: data.vm,
        models: new importer.Modeler(),
        valuta: importer.coinify,
        mediaManager: new importer.MediaManager(options.server),
        print: new importer.Print(locale, options.server, options.dev),
        controller: options.controller
      }
    }
  }
  // Get WordPress base path
  getServer (dev) {
    let uri = location.href.split('#')[0]
    let startIndex = uri.indexOf('dev_server')
    if (startIndex !== -1 && dev) {
      uri = decodeURIComponent(uri.substring(startIndex + 11))
    } else if (uri.indexOf('/wp-admin') !== -1) {
      uri = uri.split('/wp-admin')[0]
    } else {
      uri = require('../../../package.json')['wp-dev']
      if (uri === '') {
        uri = location.protocol + '//' + location.hostname + (location.port !== '' ? (':' + location.port) : '')
      }
    }
    return uri.replace('&success=1&user_id=1', '')
  }
}
export default new StorePilot()
