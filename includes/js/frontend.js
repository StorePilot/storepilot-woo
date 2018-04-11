/** Adds the modal functionality **/
if (self === top) {
  if (typeof url === 'undefined') {
    var url = '#'
  }

  // HTML

  var content = ''
  content += '<div class="sp-frontend-modal__content">'
  content += '<span class="sp-frontend-modal__close">&times;</span>'
  content += '<div id="sp-frontend-modal__loader" class="sk-cube-grid">\n' +
    '      <div class="sk-cube sk-cube1"></div>\n' +
    '      <div class="sk-cube sk-cube2"></div>\n' +
    '      <div class="sk-cube sk-cube3"></div>\n' +
    '      <div class="sk-cube sk-cube4"></div>\n' +
    '      <div class="sk-cube sk-cube5"></div>\n' +
    '      <div class="sk-cube sk-cube6"></div>\n' +
    '      <div class="sk-cube sk-cube7"></div>\n' +
    '      <div class="sk-cube sk-cube8"></div>\n' +
    '      <div class="sk-cube sk-cube9"></div>\n' +
    '    </div>'
  content += '<iframe class="sp-frontend-modal__iframe__loading" id="sp-frontend-modal__iframe" src="#" frameBorder="0"></iframe>'
  content += '</div>'

  var modal = document.createElement('div')
  modal.id = 'sp-frontend-modal'
  modal.className += 'sp-frontend-modal'
  modal.innerHTML = content

  document.body.appendChild(modal)

  // JS

  var close = document.getElementsByClassName('sp-frontend-modal__close')[0]

  var storepilot_modal_trigger = function (url) {
    var iframe = document.getElementById('sp-frontend-modal__iframe')
    var loader = document.getElementById('sp-frontend-modal__loader')
    iframe.src = url
    iframe.onload = function () {
      iframe.classList.remove('sp-frontend-modal__iframe__loading')
      loader.classList.add('sp-frontend-modal__iframe__loaded')
    }
    var el = document.getElementById('sp-frontend-modal')
    el.style.display = 'block'
  }

  close.onclick = function () {
    var el = document.getElementById('sp-frontend-modal')
    el.style.display = 'none'
  }

  window.onclick = function (event) {
    var el = document.getElementById('sp-frontend-modal')
    if (event.target === el) {
      el.style.display = 'none'
    }
  }
}
