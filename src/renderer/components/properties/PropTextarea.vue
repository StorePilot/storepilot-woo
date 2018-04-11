<template>
  <quill-editor
      v-if="mounted"
      v-model="text"
      v-loading="prop.loading"
      :options="editorOptions"
      @ready="onEditorReady($event)"
      @blur="autosave ? save() : ''">
    <div :id="editorOptions.modules.toolbar.replace('#sp-toolbar-', 'sp-toolbar-')" slot="toolbar">
      <div class="ql-toolbar" style="display: inline-block; width: calc(100% - 50px)">
        <select :title="$sp.print['SelectHeader']" class="ql-header">
          <option value="">{{$sp.print['Paragraph']}}</option>
          <option value="1"></option>
          <option value="2"></option>
          <option value="3"></option>
          <option value="4"></option>
          <option value="5"></option>
          <option value="6"></option>
        </select>
        <button :title="$sp.print['Preformatted']" class="ql-code-block">{{$sp.print['Preformatted']}}</button>
        <button :title="$sp.print['Bold']" class="ql-bold">{{$sp.print['Bold']}}</button>
        <button :title="$sp.print['Italic']" class="ql-italic">{{$sp.print['Italic']}}</button>
        <button :title="$sp.print['Underline']" class="ql-underline">{{$sp.print['BulletList']}}</button>
        <button :title="$sp.print['BulletList']" class="ql-list" value="bullet">{{$sp.print['View']}}</button>
        <button :title="$sp.print['OrderedLis']" class="ql-list" value="ordered">{{$sp.print['OrderedList']}}</button>
        <button :title="$sp.print['Quote']" class="ql-blockquote">{{$sp.print['Quote']}}</button>
        <button :title="$sp.print['AlignLeft']" class="ql-align" value="">{{$sp.print['AlignLeft']}}</button>
        <button :title="$sp.print['AlignCenter']" class="ql-align" value="center">{{$sp.print['AlignCenter']}}</button>
        <button :title="$sp.print['AlignRight']" class="ql-align" value="right">{{$sp.print['Align right']}}</button>
        <button :title="$sp.print['Link']" class="ql-link">{{$sp.print['Link']}}</button>
        <button :title="$sp.print['InsertImageFromGallery']" @click="gallery('image')" style="outline: none">
          <svg viewBox="0 0 18 18">
            <rect class="ql-stroke" height="10" width="12" x="3" y="4"></rect>
            <circle class="ql-fill" cx="6" cy="7" r="1"></circle>
            <polyline class="ql-even ql-fill" points="5 12 5 11 7 9 8 10 11 7 13 9 13 12 5 12"></polyline>
          </svg>
        </button>
        <button :title="$sp.print['InsertVideoFromGallery']" @click="gallery('video')" style="outline: none">
          <svg viewBox="0 0 18 18">
            <rect class="ql-stroke" height="12" width="12" x="3" y="3"></rect>
            <rect class="ql-fill" height="12" width="1" x="5" y="3"></rect>
            <rect class="ql-fill" height="12" width="1" x="12" y="3"></rect>
            <rect class="ql-fill" height="2" width="8" x="5" y="8"></rect>
            <rect class="ql-fill" height="1" width="3" x="3" y="5"></rect>
            <rect class="ql-fill" height="1" width="3" x="3" y="7"></rect>
            <rect class="ql-fill" height="1" width="3" x="3" y="10"></rect>
            <rect class="ql-fill" height="1" width="3" x="3" y="12"></rect>
            <rect class="ql-fill" height="1" width="3" x="12" y="5"></rect>
            <rect class="ql-fill" height="1" width="3" x="12" y="7"></rect>
            <rect class="ql-fill" height="1" width="3" x="12" y="10"></rect>
            <rect class="ql-fill" height="1" width="3" x="12" y="12"></rect>
          </svg>
        </button>
        <button :title="$sp.print['InsertLinkFromGallery']" @click="gallery('any')" style="outline: none">
          <svg viewBox="0 0 18 18">
            <path class="ql-stroke" d="M6.6,11.4L9,9a1.456,1.456,0,0,1,2.059,2.059L7.971,14.147a2.912,2.912,0,0,1-4.118-4.118l6.177-6.177a2.912,2.912,0,0,1,4.118,4.118"></path>
          </svg>
        </button>
        <button :title="$sp.print['Strikethrough']" class="ql-strike">{{$sp.print['Strikethrough']}}</button>
        <button :title="$sp.print['InsertLineBreak']" @click="insertLine">___</button>
        <select :title="$sp.print['TextColor']" class="ql-color">
          <option value=""></option>
          <option value="#e60000"></option>
          <option value="#ff9900"></option>
          <option value="#ffff00"></option>
          <option value="#008a00"></option>
          <option value="#0066cc"></option>
          <option value="#9933ff"></option>
          <option value="#ffffff"></option>
          <option value="#facccc"></option>
          <option value="#ffebcc"></option>
          <option value="#ffffcc"></option>
          <option value="#cce8cc"></option>
          <option value="#cce0f5"></option>
          <option value="#ebd6ff"></option>
          <option value="#bbbbbb"></option>
          <option value="#f06666"></option>
          <option value="#ffc266"></option>
          <option value="#ffff66"></option>
          <option value="#66b966"></option>
          <option value="#66a3e0"></option>
          <option value="#c285ff"></option>
          <option value="#888888"></option>
          <option value="#a10000"></option>
          <option value="#b26b00"></option>
          <option value="#b2b200"></option>
          <option value="#006100"></option>
          <option value="#0047b2"></option>
          <option value="#6b24b2"></option>
          <option value="#444444"></option>
          <option value="#5c0000"></option>
          <option value="#663d00"></option>
          <option value="#666600"></option>
          <option value="#003700"></option>
          <option value="#002966"></option>
          <option value="#3d1466"></option>
        </select>
        <button title="Indent left" class="ql-indent" value="-1"></button>
        <button title="Indent right" class="ql-indent" value="+1"></button>
        <button title="Insert Read More tag" @click="insertReadmore">---</button>
      </div>
      <!--div style="float: right; width: 50px;">
        <button v-if="fullscreen" title="Toggle Fullscreen" style="float: right; outline: none" @click="toggleFullscreen">
          <svg viewBox="0 0 40 40" fill="currentColor" style="vertical-align:middle;display:inline-block;fill:#666;" preserveAspectRatio="xMidYMid meet">
            <path d="m20 6.666666666666667c-7.366666666666667 0-13.333333333333334 5.97-13.333333333333334 13.333333333333332s5.966666666666667 13.333333333333336 13.333333333333334 13.333333333333336 13.333333333333336-5.969999999999999 13.333333333333336-13.333333333333336-5.966666666666669-13.333333333333334-13.333333333333336-13.333333333333334z m6.178333333333335 17.155c0.6499999999999986 0.6499999999999986 0.6499999999999986 1.7049999999999983 0 2.3566666666666656-0.3249999999999993 0.3249999999999993-0.75 0.4883333333333333-1.1783333333333346 0.4883333333333333s-0.8533333333333317-0.163333333333334-1.1783333333333346-0.4883333333333333l-3.8216666666666654-3.821666666666669-3.8216666666666654 3.821666666666669c-0.3249999999999993 0.3249999999999993-0.75 0.4883333333333333-1.1783333333333328 0.4883333333333333s-0.8533333333333335-0.163333333333334-1.1783333333333328-0.4883333333333333c-0.6500000000000004-0.6499999999999986-0.6500000000000004-1.7049999999999983 0-2.3566666666666656l3.8216666666666654-3.821666666666669-3.821666666666669-3.8216666666666654c-0.6500000000000004-0.6500000000000004-0.6500000000000004-1.705 0-2.3566666666666656s1.705-0.6500000000000004 2.3566666666666656 0l3.821666666666669 3.8216666666666654 3.8216666666666654-3.821666666666667c0.6499999999999986-0.6500000000000004 1.7049999999999983-0.6500000000000004 2.3566666666666656 0s0.6499999999999986 1.705 0 2.3566666666666674l-3.8216666666666654 3.8216666666666654 3.821666666666669 3.8216666666666654z"></path>
          </svg>
        </button>
        <button v-else title="Toggle Fullscreen" style="float: right; outline: none" @click="toggleFullscreen">
          <svg viewBox="0 0 24 24">
            <path d="M3 5v4h2v-4h4v-2h-4c-1.1 0-2 .9-2 2zm2 10h-2v4c0 1.1.9 2 2 2h4v-2h-4v-4zm14 4h-4v2h4c1.1 0 2-.9 2-2v-4h-2v4zm0-16h-4v2h4v4h2v-4c0-1.1-.9-2-2-2z"></path>
          </svg>
        </button>
      </div-->
    </div>
  </quill-editor>
</template>

<script>
  import Quill from 'quill'
  import ImageResize from '../../logic/modules/ImageResize'
  import Hr from '../../logic/modules/Hr'
  import More from '../../logic/modules/More'
  Quill.register('modules/imageResize', ImageResize)
  Quill.register('formats/hr', Hr)
  Quill.register('formats/more', More)
  export default {
    props: [
      'prop',
      'raw',
      'autosave',
      'placeholder'
    ],
    name: 'prop-textarea',
    data () {
      return {
        firstParse: true,
        mounted: false,
        editorOptions: {},
        fullscreen: false,
        editor: null,
        text: 'test',
        ph: this.placeholder
      }
    },
    mounted () {
      if (typeof this.placeholder === 'undefined') {
        this.ph = this.$sp.print['StartComposing']
      } else {
        this.ph = this.placeholder
      }
      let scope = this
      this.text = ' '
      setTimeout(() => {
        let randId = '#sp-toolbar-' + Math.random().toString(36).substr(2, 5)
        scope.editorOptions = {
          placeholder: scope.ph,
          modules: {
            toolbar: randId,
            imageResize: {
              displaySize: true
            }
          }
        }
        scope.mounted = true
      }, 500)
    },
    watch: {
      'prop.value' (val) {
        if (val !== null && (typeof this.prop.html === 'undefined' || this.prop.html === null)) {
          this.prop.html = val
        }
      },
      'text' (val) {
        if (this.firstParse) {
          this.prop.value = this.encode(val)
          this.prop.changed(false)
          this.firstParse = false
        } else {
          this.prop.value = this.encode(val)
        }
      },
      'raw.value' () {
        if (typeof this.raw !== 'undefined') {
          if (this.editor !== null) {
            this.editor.setText('')
            this.editor.clipboard.dangerouslyPasteHTML(this.parse(this.raw.value))
          }
          this.prop.html = this.prop.value
          this.prop.value = this.encode(this.text)
          this.prop.changed(false)
          this.firstParse = false
        }
      }
    },
    methods: {
      save () {
        this.prop.html = null
        this.prop.save().then(() => {
          if (typeof this.raw !== 'undefined') {
            this.raw.fetch()
          }
        })
      },
      toggleFullscreen () {
        this.fullscreen = !this.fullscreen
      },
      gallery (type) {
        if (type === 'image') {
          this.$sp.mediaManager.getMedia(
            'image',
            false,
            this.$sp.print['SelectImage'],
            this.$sp.print['UseSelectedMedia']
          ).then(media => {
            if (media !== null) {
              this.insertImage('image', media.url)
            }
          })
        } else if (type === 'video') {
          this.$sp.mediaManager.getMedia(
            'video',
            false,
            this.$sp.print['SelectVideo'],
            this.$sp.print['UseSelectedMedia']
          ).then(media => {
            if (media !== null) {
              this.insertVideo(media.url)
            }
          })
        } else {
          this.$sp.mediaManager.getMedia(
            '',
            false,
            this.$sp.print['SelectFile'],
            this.$sp.print['UseSelectedMedia']
          ).then(media => {
            if (media !== null) {
              this.insertLink(media.url, media.name)
            }
          })
        }
      },
      onEditorReady (editor) {
        this.editor = editor
        editor.setText('')
        if (typeof this.raw !== 'undefined') {
          editor.clipboard.dangerouslyPasteHTML(this.parse(this.raw.value))
        }
        this.firstParse = true
      },
      insert (val) {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.insertText(range.index, val)
        }
      },
      insertVideo (src) {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.insertText(range.index,
            '[video width="1280" height="720" ' + src.split('.').pop() + '="' + src + '"][/video]'
          )
        }
      },
      insertLink (src, name) {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.clipboard.dangerouslyPasteHTML(range.index,
            '<a target="_blank" href="' + src + '">' + name + '</a>'
          )
        }
      },
      insertLine () {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.clipboard.dangerouslyPasteHTML(range.index,
            '<hr />'
          )
        }
      },
      insertImage (type, val) {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.insertEmbed(range.index, type, val)
        }
      },
      insertReadmore () {
        if (this.editor !== null) {
          let range = this.editor.getSelection(true)
          this.editor.clipboard.dangerouslyPasteHTML(range.index,
            '<more />'
          )
        }
      },
      encode (val) {
        if (val === null) {
          val = ''
        }
        return val
          .replace(/<p><br><\/p>/g, '<br><br>')
          .replace(/<\/p><p>/g, '<br>')
          .replace(/<br>/g, '\n')
          .replace(/<p>/g, '')
          .replace(/<\/p>/g, '')
          .replace(/ class=""/g, '')
          .replace(/<more><\/more>/g, '<!--more-->')
      },
      parse (val) {
        if (val === null) {
          val = ''
        }
        return ('<p>' +
          val
            .replace(/\n/g, '<br>')
            .replace(/<!--more-->/g, '<more></more>')
            .replace(/<hr \/>/g, '<hr>')
            .replace(/<hr\/>/g, '<hr>')
            .replace(/<br><br>/g, '<br-recursive>')
            .replace(/<br-recursive><br>/g, '<br-recursive>')
            .replace(/<br>/g, '</p><p>')
            .replace(/<p><\/p>/g, '<p><br></p>')
            .replace(/<br-recursive>/g, '</p><p><br></p><p>') +
          '</p>'
        )
          .replace(/<p><more><\/more><\/p>/g, '<more></more>')
          .replace(/<p><hr><\/p>/g, '<hr>')
      }
    }
  }
</script>

<style>
  .el-tabs__content {
    overflow: visible;
  }
  .ql-editor {
    min-height: 6em;
    max-height: 25em;
    background-color: #fdfdfd;
  }
  .ql-editor hr {
    border-bottom: 1px solid black;
  }
  .ql-editor more {
    display: block;
    width: 100%;
    height: 20px;
    background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAB2wAAAAUCAMAAAB28j3QAAAAPFBMVEVMaXG7yd/fybu7wdL/6s67u7vSwbv/3sjI3v/O6v+7u8nB0u7Bu7vJu7u7u8Hu0sHBycm7ydLJybvJu8Gz2eUyAAAACnRSTlMA////P///s7M/0Um8/wAAAQdJREFUeNrt28tuwjAUBNCJzU1CEqCP///XLqhUlS5pKx7nbDxbW4pGtuMEAAAAAAAAAAAA4L+NtSbJqXqSqapqTjJW1XF/Hmu2SgBwVdn2JDlUT041J1OtyTgkbTmPAMB1Zfuyzcm0vPXkMCRfJXs67pUtAPxC2Q5tSNrQenbbmiTTZ8mO3c4W7klVVdXT5CebrnzH+Vy205K871vPbpuTZKo5Y1UNOd/ZLs/6KcsPkZ9ptspWlm+4bHOYp54fO9u25HJna91kZXsPXzVwg8fIaa/jmnZ5Z7vbVsfIAPA7ZTvVcZ/mb2QA+LOyzaEn7fs72yHZbYN3tgAAAAAAAAAAAAAA8OA+APMLKOOI5E5PAAAAAElFTkSuQmCC');
    background-position: center center;
  }
  .fullscreen .ql-toolbar {
    background: #fff;
  }
  .fullscreen .ql-editor, .fullscreen .ql-container, .fullscreen .quill-editor {
    height: 100%;
    max-height: 100%;
  }
</style>
