<template>
  <div @click="selectMedia">
    <div @click="$event.preventDefault()">
      <el-upload
          :class="'sp-uploader sp-upload-btn'"
          ref="upload"
          drag
          action="#"
          :multiple="multiple"
          :http-request="upload">
        <i class="el-icon-upload"></i>
        <!--div class="el-upload__text" style="font-size: 10px;">
          Drop {{multiple ? 'files' : 'file'}} here or
          <em>click to upload</em>
        </div-->
      </el-upload>
    </div>
  </div>
</template>

<script>
  export default {
    props: [
      'prop',
      'library',
      'autosave',
      'multiple',
      'title',
      'buttonText',
      'image'
    ],
    name: 'prop-media',
    data () {
      return {
        open: false
      }
    },
    methods: {
      selectMedia () {
        if (!this.open) {
          this.$nextTick(() => {
            this.open = false
          })
          this.$sp.mediaManager.getMedia(
            this.library,
            this.multiple,
            this.title,
            this.buttonText
          ).then(media => {
            if (media !== null) {
              if (this.multiple) {
                let i = 0
                media.forEach(m => {
                  if (m !== null) {
                    // Removes placeholder if set
                    if (
                      this.prop.value.length === 1 &&
                      typeof this.prop.value[0].src !== 'undefined' &&
                      this.prop.value[0].src.indexOf('placeholder.png') !== -1
                    ) {
                      this.prop.value.shift()
                    }
                    let f = {
                      name: m.name,
                      file: m.url
                    }
                    if (this.image) {
                      f = {
                        title: m.title,
                        name: m.name,
                        id: m.id,
                        alt: m.alt_text,
                        file: m.url,
                        src: m.url,
                        position: i
                      }
                    }
                    if (typeof m.sizes !== 'undefined' && this.image) {
                      f.src_thumbnail = typeof m.sizes.thumbnail !== 'undefined' ? m.sizes.thumbnail.url : m.url
                      f.src_shop_thumbnail = typeof m.sizes.thumbnail !== 'undefined' ? m.sizes.thumbnail.url : m.url
                      f.src_medium = typeof m.sizes.medium !== 'undefined' ? m.sizes.medium.url : m.url
                      f.src_large = typeof m.sizes.large !== 'undefined' ? m.sizes.large.url : m.url
                    }
                    if (this.prop.value === 0) {
                      this.prop.value = [f]
                    } else {
                      this.prop.value.push(f)
                    }
                    i++
                  }
                })
              } else {
                let f = {
                  name: media.name,
                  file: media.url
                }
                if (this.image) {
                  f = {
                    title: media.title,
                    name: media.name,
                    id: media.id,
                    alt: media.alt_text,
                    file: media.url,
                    src: media.url,
                    position: 0
                  }
                }
                if (typeof media.sizes !== 'undefined' && this.image) {
                  f.src_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
                  f.src_shop_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
                  f.src_medium = typeof media.sizes.medium !== 'undefined' ? media.sizes.medium.url : media.url
                  f.src_large = typeof media.sizes.large !== 'undefined' ? media.sizes.large.url : media.url
                }
                this.prop.value = f
              }
              if (this.autosave) {
                this.prop.save()
              }
            }
          })
        }
        this.open = true
      },
      upload (data) {
        let file = new FormData()
        file.append('file', data.file)
        let media = new this.$sp.models.Media(this.$pap.controller, null, 'sp')
        media.upload(file).then(media => {
          media = media.data
          if (this.multiple) {
            // Removes placeholder if set
            if (
              this.prop.value.length === 1 &&
              typeof this.prop.value[0].src !== 'undefined' &&
              this.prop.value[0].src.indexOf('placeholder.png') !== -1
            ) {
              this.prop.value.shift()
            }
            let f = {
              name: media.name,
              file: media.url
            }
            if (this.image) {
              f = {
                title: media.title,
                name: media.name,
                id: media.id,
                alt: media.alt_text,
                file: media.url,
                src: media.url
              }
            }
            if (typeof media.sizes !== 'undefined' && this.image) {
              f.src_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
              f.src_shop_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
              f.src_medium = typeof media.sizes.medium !== 'undefined' ? media.sizes.medium.url : media.url
              f.src_large = typeof media.sizes.large !== 'undefined' ? media.sizes.large.url : media.url
            }
            this.prop.value.push(f)
          } else {
            let f = {
              name: media.name,
              file: media.url
            }
            if (this.image) {
              f = {
                title: media.title,
                name: media.name,
                id: media.id,
                alt: media.alt_text,
                file: media.url,
                src: media.url
              }
            }
            if (typeof media.sizes !== 'undefined' && this.image) {
              f.src_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
              f.src_shop_thumbnail = typeof media.sizes.thumbnail !== 'undefined' ? media.sizes.thumbnail.url : media.url
              f.src_medium = typeof media.sizes.medium !== 'undefined' ? media.sizes.medium.url : media.url
              f.src_large = typeof media.sizes.large !== 'undefined' ? media.sizes.large.url : media.url
            }
            this.prop.value = f
          }
          if (this.autosave) {
            this.prop.save()
          }
        })
      }
    }
  }
</script>

<style>
  .sp-upload-btn {
    width: 70px;
    height: 70px;
  }
  .sp-uploader {
    background-color: #fff;
    box-sizing: border-box;
    text-align: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    color: #97a8be;
    font-size: 14px;
    height: 70px;
  }
  .sp-uploader em {
    color: #20a0ff;
    font-style: normal;
  }
  .sp-uploader .el-icon-upload {
    margin: 10px 0 5px!important;
    font-size: 30px!important;
  }
  .sp-uploader:hover {
    border-color: #20a0ff;
  }
  .el-upload-list {
    display: none!important;
  }
  .el-upload-dragger {
    width: 70px;
    height: 70px;
  }
  .avatar-uploader-icon {
    position: absolute;
    font-size: 30px;
    color: #8c939d;
    width: 100%;
    height: 100%;
    top: 40%;
    text-align: center;
  }
</style>
