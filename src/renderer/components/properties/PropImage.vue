<template>
  <div>
    <div style="width: 100%; display: inline-block;" class="sp-image">
      <div
          @contextmenu.prevent="$refs.ctx.open($event)"
          v-if="prop.value !== 0 && prop.value !== null && typeof prop.value.src !== 'undefined' && (prop.value.name!=='Placeholder'||prop.value.alt!=='Placeholder'||prop.value.id!==0)"
          v-loading="prop.loading"
          style="width: 70px; height: 70px; margin: 0 10px 10px 0; display: inline-block; float:left; background-color: #7da3bd;"
          v-lazy:background-image="prop.value.src_shop_thumbnail"
          :style="'background-size: cover'">
        <el-button
            class="sp-delete-btn"
            @click="prop.value = 0; autosave ? prop.save() : ''"
            size="mini"
            type="danger"
            icon="el-icon-delete"></el-button>
      </div>
      <prop-media
          style="float: left;"
          :title="$sp.print['SelectImage']"
          library="image"
          :image="true"
          :multiple="false"
          :autosave="autosave"
          :prop="prop"
      ></prop-media>
    </div>
    <context-menu
        class="sp-context-menu"
        ref="ctx"
        @ctx-open="ctxOpen">
      <li @click="prop.value = 0; autosave ? prop.save() : ''">{{$sp.print['Remove']}}</li>
      <li @click="copy(prop.value.src)">{{$sp.print['CopyLink']}}</li>
      <li @click="open(prop.value.src)">{{$sp.print['View']}}</li>
    </context-menu>
  </div>
</template>

<script>
  import propMedia from './PropMedia'
  export default {
    props: [
      'prop',
      'autosave'
    ],
    name: 'prop-image',
    components: {
      propMedia
    },
    methods: {
      open (url) {
        window.open(url, '_blank')
      },
      ctxOpen (data) {
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
      }
    }
  }
</script>

<style>
  .sp-image .sp-delete-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 1000;
    opacity: 0;
  }
  .sp-image div {
    position: relative;
  }
  .sp-image div:hover .sp-delete-btn{
    opacity: 1;
  }
</style>
