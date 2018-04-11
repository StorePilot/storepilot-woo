/**
 * Caller to let StorePilot fetch Integrated Systems default Media Manager
 * @note - Must be implemented manually at each backend
 */
export default class MediaManager {
  constructor (server = document.location.href) {
    this.server = server
    let resolveResponse
    let response = new Promise(function (resolve) {
      resolveResponse = resolve
    })
    let receiveMedia = function (event) {
      let data
      try {
        data = JSON.parse(event.data)
      } catch (e) {
        data = null
      }
      if (data !== null && data.type === 'sp_send_media') {
        if (!data.multiple) {
          if (data.media !== null && data.media.constructor === Array) {
            resolveResponse(data.media[0])
            response = new Promise(function (resolve) {
              resolveResponse = resolve
            })
          } else {
            resolveResponse(data.media)
            response = new Promise(function (resolve) {
              resolveResponse = resolve
            })
          }
        } else {
          if (data.media !== null && data.media.constructor === Array) {
            resolveResponse(data.media)
            response = new Promise(function (resolve) {
              resolveResponse = resolve
            })
          } else {
            resolveResponse([data.media])
            response = new Promise(function (resolve) {
              resolveResponse = resolve
            })
          }
        }
      }
    }
    this.getMedia = function (library = 'image', multiple = false, title = 'Select media', buttonText = 'Use selected media') {
      let data = {
        type: 'sp_get_media',
        multiple: multiple,
        title: title,
        buttonText: buttonText,
        library: library
      }
      parent.window.postMessage(JSON.stringify(data), this.server)
      return response
    }
    window.addEventListener('message', receiveMedia, false)
  }
}
