/**
 * Prints translated words based on locale
 * Translation could be set by localized variable i18n or json in translations dir.
 */
import * as importer from './Importer'
let localeDefault = 'enUS'
export default class Print {
  constructor (locale = localeDefault, server = '', dev) {
    this.localize(locale, false)
    if (self !== top && dev) {
      let receiveLocale = (event) => {
        let json
        try {
          json = JSON.parse(event.data)
        } catch (e) {
          json = null
        }
        if (json !== null && json.type === 'i18n') {
          this.localize(json.data, false)
        }
      }
      window.addEventListener('message', receiveLocale, false)
      parent.window.postMessage(JSON.stringify({
        type: 'sp_i18n'
      }), server)
    }
  }

  /**
   * Set localized translation
   * @param obj: object | string - Language Code or Translation object
   * @param force: boolean (true) - Force using input, dont look for localized script
   */
  localize (obj, force = true) {
    if (obj !== null && obj.constructor === Object) {
      Object.keys(obj).forEach(key => {
        if (key !== 'localize') {
          this[key] = obj[key]
        } else {
          this.__localize = obj[key]
        }
      })
    } else if (obj !== null && typeof obj === 'string') {
      /* eslint-disable */
      if (!force && typeof i18n !== 'undefined') {
        this.localize(i18n)
      } else {
        this.localize( // Get translation by input value
          typeof importer[obj] !== 'undefined' ?
            importer[obj] : importer[localeDefault]
        )
      }
      /* eslint-enable */
    }
  }
}
