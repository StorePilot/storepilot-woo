var jQuery
var hasWoocommerce
var prettyPermalinks
var i18n
var preload
jQuery(document).on('ready', function () {
  if (!hasWoocommerce) {
    let url = document.createElement('a')
    url.setAttribute('href', jQuery('#hotreload').attr('src'))
    url.hash = 'activate'
    location.hash = 'activate'
  } else if (!prettyPermalinks) {
    let url = document.createElement('a')
    url.setAttribute('href', jQuery('#hotreload').attr('src'))
    url.hash = 'permalinks'
    location.hash = 'permalinks'
  }
  function receiveHashChangeMessage (event) {
    let json
    try {
      json = JSON.parse(event.data)
    } catch (e) {
      json = null
    }
    if (json !== null && json.type === 'sp_hash_change' && location.hash !== json.hash) {
      location.hash = json.hash
    } else if (json !== null && json.type === 'sp_i18n' && typeof i18n !== 'undefined') {
      event.source.postMessage(JSON.stringify({
        type: 'i18n',
        data: i18n
      }), event.origin)
    } else if (json !== null && json.type === 'sp_preload' && typeof preload !== 'undefined') {
      event.source.postMessage(JSON.stringify({
        type: 'preload',
        data: preload
      }), event.origin)
    }
  }
  window.addEventListener('message', receiveHashChangeMessage, false)
  jQuery(window).on('hashchange', function () {
    let url = document.createElement('a')
    let hotreload = jQuery('#hotreload')
    url.setAttribute('href', hotreload.attr('src'))
    url.hash = location.hash
    hotreload.attr('src', url)
  })
})
