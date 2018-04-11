<template>
  <div :class="'sp-' + (typeof size === 'undefined' ? 'large' : size)">
    <div class="sp-product-images">
      <draggable
        v-if="prop.value !== null"
        v-model="prop.value"
        @end="dropEnd"
        :options="{scroll: true, group: {name: 'gallery', pull: 'clone', put: false}}">
        <div
            @contextmenu.prevent="$refs.ctx.open($event, {index: index, url: image.src})"
            v-if="image.name!=='Placeholder'&&image.alt!=='Placeholder'&&image.id!==0"
            v-for="(image, index) in prop.value"
            v-loading="prop.loading"
            :data-id="image.id + '-' + index"
            :key="image.id + '-' + index"
            class="sp-gallery-image"
            v-lazy:background-image="image.src_shop_thumbnail"
            :style="'background-size: cover;'">
          <el-button
              v-if="prop.value.length > 1 && index > 0"
              class="sp-delete-btn"
              @click="remove(index)"
              size="mini"
              type="danger"
              icon="el-icon-delete">
          </el-button>
        </div>
      </draggable>
    </div>
    <div style="margin: 0 0 10px 0; display: inline-block;">
      <prop-media
          :title="$sp.print['SelectImages']"
          library="image"
          :image="true"
          :multiple="true"
          :autosave="autosave"
          :prop="prop"
      ></prop-media>
    </div>
    <context-menu
        class="sp-context-menu"
        ref="ctx"
        @ctx-open="ctxOpen">
      <li @click="remove(menu.index)">{{$sp.print['Remove']}}</li>
      <li @click="copy(menu.url)">{{$sp.print['CopyLink']}}</li>
      <li @click="open(menu.url)">{{$sp.print['View']}}</li>
    </context-menu>
  </div>
</template>

<script>
  import draggable from 'vuedraggable'
  import propMedia from './PropMedia'
  export default {
    props: [
      'prop',
      'autosave',
      'size'
    ],
    name: 'prop-gallery',
    components: {
      propMedia,
      draggable
    },
    data () {
      return {
        menu: null
      }
    },
    methods: {
      open (url) {
        window.open(url, '_blank')
      },
      ctxOpen (data) {
        this.menu = data
      },
      copy (url) {
        let scope = this
        if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
          let textarea = document.createElement('textarea')
          textarea.textContent = url
          textarea.style.position = 'fixed' // Prevent scrolling to bottom of page in MS Edge.
          document.body.appendChild(textarea)
          textarea.select()
          try {
            document.execCommand('copy') // Security exception may be thrown by some browsers.
          } catch (ex) {
            scope.$message({
              type: 'error',
              message: 'Your browser doesnt support this type of copy'
            })
          } finally {
            document.body.removeChild(textarea)
          }
        }
      },
      remove (index) {
        this.prop.value.splice(index, 1)
        if (this.autosave) {
          this.prop.save()
        }
      },
      dropEnd () {
        let i = 0
        this.prop.value.forEach(img => {
          img.position = i
          i++
        })
        if (this.autosave) {
          this.prop.save()
        }
      }
    }
  }
</script>

<style>
  .sp-product-images .sp-delete-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 1000;
    opacity: 0;
    padding: 5px;
  }
  .sp-product-images div, .sp-product-images .sp-gallery-image {
    position: relative;
  }
  .sp-gallery-image:hover .sp-delete-btn {
    opacity: 1;
  }
  .sp-product-images .ui-sortable-placeholder {
    width: 70px;
    height: 70px;
    float: left;
    display: inline-block;
    margin: 0 10px 10px 0;
  }
  .sp-gallery-image {
    width: 70px;
    height: 70px;
    margin: 0 10px 10px 0;
    display: inline-block;
    float:left;
    background-color: #b1b9bf;
  }
  .sp-small .sp-product-images .sp-delete-btn {
    top: 1px;
    left: 1px;
    padding: 1px;
  }
  .sp-small .sp-product-images .ui-sortable-placeholder {
    width: 30px;
    height: 30px;
    margin: 0 3px 3px 0;
  }
  .sp-small .sp-gallery-image {
    width: 30px;
    height: 30px;
  }
  .sp-gallery-image:hover {
    cursor: move;
  }
  .sp-small .sp-upload-btn, .sp-small .el-upload, .sp-small .el-upload-dragger {
    width: 30px;
    height: 30px;
  }
  .sp-small .sp-uploader, .sp-small .sp-manager {
    height: 30px;
  }
  .sp-small .sp-uploader .el-icon-upload, .sp-small .sp-manager .el-icon-upload {
    margin: 7px 0 5px!important;
    font-size: 15px!important;
    line-height: 15px;
  }
  .sp-small .sp-select-text, .sp-small .el-upload__text, .sp-small .el-upload__tip {
    display: none!important;
  }
</style>
