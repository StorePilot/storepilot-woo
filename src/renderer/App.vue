<template>
  <div id="storepilot" class="storepilot sp" :class="$sp.dev ? 'sp-development' : 'sp-production'">
    <fullscreen ref="fullscreen" class="sp-fullscreenwrapper">
      <div class="sp-header">
        <div class="sp-logo">
          <strong>StorePilot</strong> {{$sp.print['Manager']}} <span> {{ version }}</span>
        </div>

        <div class="sp-main-menu">
          <!-- Add menu in the future here -->
        </div>

        <span class="sp-autosave-label">{{$sp.print['Autosave']}}</span>
        <el-switch
            v-model="$sp.data.autosave"
            style="float: right"
            active-text=""
            inactive-text="">
        </el-switch>
        <el-button-group>
          <el-button
              :title="$sp.print['Refresh'] +': ' + (mac ? 'cmd + s' : 'ctrl + s')"
              @click="$sp.data.exec.save()"
              size="mini"
              v-if="!$sp.data.autosave"
              :disabled="$sp.data.autosave || $sp.data.changes === 0"
              type="primary">
            {{$sp.print['Save']}}
          </el-button>
          <el-button
              :title="$sp.print['Refresh']"
              size="mini"
              @click="$sp.data.exec.refresh()"
              :icon="'el-icon-refresh'"></el-button>
          <el-button
            title="Open in new tab"
            size="mini"
            @click="win.open(
              loc.href
                .replace('page=storepilot_fill', 'page=storepilot')
                .replace('page=storepilot', 'page=storepilot_fill'),
              '_blank'
            )"
            :icon="'el-icon-news'"></el-button>
        </el-button-group>
      </div>

      <warnings
          v-if="mountedSafe"
          :activated="activated"
          :authenticated="(authenticated.wc&&authenticated.sp) || !$sp.dev"
          :mounted="mountedSafe"
      ></warnings>
      <router-view
          @auth="auth"
          @permalinks="permalinks"
          :permalinks="activated.pl"
          :activated="activated.wc"
          :authenticated="(authenticated.wc&&authenticated.sp)">
      </router-view>
    </fullscreen>
  </div>
</template>

<script>
  import navigation from './components/Navigation'
  import warnings from './components/Warnings'
  export default {
    components: {
      navigation,
      warnings
    },
    name: 'storepilot',
    data () {
      return {
        win: window,
        loc: location,
        fullscreen: false,
        mac: window.navigator.platform.toLowerCase().indexOf('mac') !== -1,
        authenticated: {
          wc: false,
          sp: false
        },
        activated: {
          wc: this.$sp.dev,
          pl: this.$sp.dev
        },
        mountedSafe: false,
        version: require('../../package.json').version
      }
    },
    mounted () {
      if (!this.$sp.dev) {
        // Check Dependencies
        this.activated.wc = (document.getElementById('storepilot-system').getAttribute('woocommerce') === 'true')
        this.activated.pl = (document.getElementById('storepilot-system').getAttribute('permalinks') === 'true')
      } else {
        this.$watch('$route', () => {
          parent.window.postMessage(JSON.stringify({
            type: 'sp_hash_change',
            hash: location.hash
          }), this.$sp.server)
        })
      }
      // Timeout to hide Warnings while loading
      setTimeout(() => {
        this.mountedSafe = true
      }, 1500)
    },
    created () {
      this.$pap.controller.config({
        base: this.$sp.server
      })
      let index = new this.$pap.Endpoint('index', this.$pap.controller, null, { batch: 'batch' })
      // Check if authorized
      index.fetch().then(response => {
        this.authenticated['wc'] = true
        this.authenticated['sp'] = true
      }).catch(e => {
        this.authenticated['wc'] = false
        this.authenticated['sp'] = false
      })
      this.saveKeyListener()
    },
    methods: {
      auth (val) {
        this.activated.wc = val
      },
      permalinks (val) {
        this.activated.pl = val
      },
      saveKeyListener () {
        document.addEventListener('keydown', (e) => {
          if (e.keyCode === 83 && (navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey)) {
            e.preventDefault()
            this.$sp.data.exec.save()
          }
        }, false)
      }
    }
  }
</script>

<style>
  body {
    margin: 0;
    padding: 0;
    line-height: 1em!important;
  }
  body, html,

    /** storepilot hooked **/
  .storepilot, .sp-fullscreenwrapper {
    background-color: #fff;
    color:#333;
    height: 100%;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    flex: 1 1 auto;
  }
  .storepilot, .sp-fullscreenwrapper {
    font-size: 15px;
    display: flex;
    flex-direction: column;
  }
  .storepilot h1, .storepilot h2, .storepilot h3 {
    margin:0;
    padding: 0 0 10px;
  }
  .storepilot h2 {
    font-size: 18px;
  }
  .storepilot h3 {
    font-size: 16px;
  }

  /** sp tagged **/
  .sp-header {
    display: flex;
    align-items: center;
    text-shadow: 1px 1px 1px rgba(0,0,0,.1);
    min-height: 50px;
    padding: 0 10px;
    background-color: #40454c;
    border-bottom: 1px solid rgba(0,0,0,.1);
    color:#FFF;
    background: linear-gradient(to right, #224267, #537ca2);
  }
  .sp-header > * {
    margin: 0 7.5px;
  }
  .sp-main-menu {
    margin-left: 30px;
    margin-right: auto;
  }
  .sp-navigator {
    position: relative;
    overflow-y: auto;
    overflow-x: hidden;
    background-color: #f9f9f9;
    padding-bottom: 60px;
  }
  .sp-navigator .sp-bottom-nav {
    position: fixed;
    bottom: 0;
    background-color: rgba(249, 249, 249, 0.7);
  }
  .sp-explorer {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    padding: 20px;
  }
  .sp-header-padding h3 {
    padding-top: 10px;
    padding-bottom: 7px;
    display: block;
  }
  .sp-editor {
    flex: 1;
    overflow-x: hidden;
    overflow-y: auto;
    background-color: #f9f9f9;
  }
  .sp-editor .el-tabs__content{
    padding: 10px 20px;
  }
  .sp-editor .sp-sub-header {
    padding: 20px 20px 0;
  }
  .sp-editor .el-tabs__header {
    padding: 0 0 0 10px;
  }
  .sp-editor-id {
    position: relative;
    margin: 0 0 10px;
    float: right;
    color: gray;
    padding: 10px;
    opacity: .5;
  }
  .sp-editor-title {
    display: block;
    background: transparent!important;
    background-image: none;
    border: none;
    padding: 0;
    margin: 20px 0 14px;
    font-weight: bold;
    text-overflow: ellipsis;
    font-size: 1.5em;
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
  }
  .sp-browser-wrapper,
  .el-tabs__content {
    flex: 1;
    overflow-x: hidden;
    overflow-y: auto;
  }
  .sp-sub-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
  }
  .sp-content-padding {
    padding: 20px;
  }
  .sp-sub-header-image {
    background-size: cover!important;
    background-color: #b1b9bf;
    margin-right: 15px;
    width: 70px;
    height: 70px;
  }
  .sp-title-wrapper{
    flex: 1;
    margin-right: auto;
  }
  .sp-products-list-product .sp-sale-label {
    position: absolute!important;
  }
  .sp-products-list-product .sp-status-label {
    position: absolute!important;
    padding: 3px;
  }
  .sp-delete-warning {
    display: block;
    margin-bottom: 20px;
  }
  .sp-notify {
    font-size: .9em;
    opacity: .9;
  }
  .sp-dragover {
    background: rgba(32, 160, 255, .5);
  }
  .sp-explorer {
    background: #fff;
  }
  .sp-navigator .el-tabs__header {
    padding: 0 10px !important;
  }
  .sp-menu-item-count {
    opacity: .5;
    font-size: .9em;
    margin-left: 2px;
    display: inline-block;
    margin-bottom: 0px;
  }
  .sp .el-menu-item.is-active{
    font-weight: bold;
  }
  .sp-context-menu .ctx-menu {
    min-width: 200px;
    padding: 0;
    border-radius: 0;
    border: none;
  }
  .sp-context-menu li {
    padding: 10px;
  }
  .sp-context-menu li:hover {
    background: #20a0ff;
    cursor: pointer;
    color: #fff;
  }
  .sp-modal, .sp-modal.el-dialog__wrapper {
    max-height: 90%;
    top: 5%;
    z-index: 10000!important;
  }
  .sp-modal-top, .sp-modal-top.el-dialog__wrapper {
    z-index: 10001!important;
  }
  .sp-modal .el-tabs__nav-wrap {
    padding: 0 20px;
  }
  .sp-modal .actions li {
    padding: 0 8px;
  }
  .sp-modal .actions li a {
    padding: 0;
  }
  .sp-modal .el-dialog {
    background: #f9f9f9
  }

  /** el tagged **/
  .el-upload-dragger, .el-upload.el-upload--text {
    width: 100%!important;
    height: 100%!important;
  }
  .el-message {
    top: 40px!important;
  }
  .el-tabs__content {
    overflow: visible;
  }
  .el-tabs__header{
    position: sticky!important;
    top:0;
    z-index: 99;
    background-color: #f9f9f9;
  }
  .el-menu-item,
  .el-submenu__title {
    height: 46px!important;
    line-height: 46px!important;
    border-bottom: 1px solid rgba(0,0,0,.03);
  }
  .el-switch__input {
    display: none!important;
  }
  .el-dialog__wrapper {
    z-index: 10000!important;
  }
  .el-autocomplete-suggestion.el-popper, .el-select-dropdown, .el-popover {
    z-index: 2147483647!important;
  }
  .el-table td, .el-table th.is-leaf {
    border-bottom: none!important;
  }

  /** Others **/
  .sortable-drag {
    opacity: .99; /** Removing borders from sortablejs. Do not set to 1 **/
  }

  /** Footer Menu **/
  .flex {
    display: flex;
  }
  @media (max-width: 768px) {
    .flex {
      display: block;
    }
  }
</style>
