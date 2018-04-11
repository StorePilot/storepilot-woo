<template>
  <el-col
      class="sp-editor-wrapper"
      v-loading="data[tag] !== null && data[tag].loading">

    <span class="sp-editor-id" v-if="data[tag] !== null">
      #{{data[tag].id.value}}
    </span>

    <div class="sp-sub-header" v-if="data[tag] !== null">
      <div class="sp-title-wrapper">
        <prop-title
            :placeholder="$sp.print['Title']"
            :prop="data[tag].name"
            :autosave="$sp.data.autosave"
        ></prop-title>
        <ul class="actions" style="width: calc(100% - 74px - 20px);">
          <li><a :href="data[tag].id.value !== null ? ($sp.server + '/wp-admin/term.php?taxonomy=product_tag&tag_ID=' + data[tag].id.value + '&post_type=product') : '#'" target="_blank">{{$sp.print['Advanced']}}</a></li>
          <li @click="deleteForce = true">{{$sp.print['DeletePermanently']}}</li>
        </ul>
      </div>
    </div>

    <div class="sp-content-padding" v-if="data[tag] !== null">
      <table
          cellspacing="0"
          style="width: 100%; border-collapse: collapse"
          class="sp-options">
        <tbody style="width: 100%;">
        <tr>
          <td>
            <h3>{{$sp.print['TagSlug']}}</h3>
          </td>
          <td>
            <el-input
                :placeholder="$sp.print['Slug']"
                v-loading="data[tag].slug.loading"
                v-model="data[tag].slug.value"
                @blur="$sp.data.autosave ? data[tag].slug.save() : ''">
            </el-input>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <el-dialog
        :title="$sp.print['DeletePermanently']"
        :visible.sync="deleteForce">
      <el-form>
        <span class="sp-delete-warning">{{$sp.print['ThisCanNotBeUndone']}}</span>
        <el-button @click="deleteForce = false">{{$sp.print['Cancel']}}</el-button>
        <el-button style="float: right" autofocus @click="remove(data[tag])">{{$sp.print['Delete']}}</el-button>
      </el-form>
    </el-dialog>
  </el-col>
</template>

<script>
  import propTitle from '../../../../components/properties/PropTitle'
  export default {
    name: 'Tag',
    props: [
      'data',
      'tag',
      'id'
    ],
    components: {
      propTitle
    },
    data () {
      return {
        deleteForce: false
      }
    },
    created () {
      if (this.id !== null && typeof this.id !== 'undefined' && this.data[this.tag] === null) {
        this.data.tab = 'tags'
        this.data[this.tag] = new this.$sp.models.ProductTag(this.$pap.controller, this.id)
        this.data[this.tag].fetch()
      } else if (this.id !== null && typeof this.id !== 'undefined' && this.data[this.tag] !== null && this.id !== this.data[this.tag].id.value) {
        this.data[this.tag].id.value = this.id
        this.data[this.tag].fetch()
      }
    },
    computed: {
      changes () {
        if (this.data[this.tag] !== null) {
          return this.data[this.tag].changes(false, true).length
        } else {
          return 0
        }
      }
    },
    watch: {
      '$sp.data.on.refresh' () {
        this.data[this.tag].fetch()
      },
      '$sp.data.on.save' () {
        this.data[this.tag].save().then(() => {
          this.$sp.data.changes = this.data[this.tag].changes(false, true).length
          this.data.refresh.tags = !this.data.refresh.tags
        })
      },
      'changes' (changes) {
        this.$sp.data.changes = changes
      }
    },
    methods: {
      getTags (page = 1) {
        let tags = new this.$pap.List(this.$sp.models.ProductTag, this.$pap.controller, null, { batch: 'batch' })
        let scope = this
        let fetch = () => {
          tags.query(false)
            .page(page)
            .order('asc')
            .custom('filter[orderby]=' + 'menu_order title')
            .perPage(100)
            .fetch().then(() => {
              if (page < tags.pages) {
                page++
                fetch()
              } else {
                scope.$sp.data.tags = tags
              }
            })
        }
        fetch()
      },
      remove (item) {
        item.remove(null, [{ key: 'force', value: true }]).then(() => {
          this.getTags()
          if (this.$sp.data.tags.children.length > 0) {
            this.data[this.tag] = this.$sp.data.tags.children[0]
          } else {
            this.data[this.tag] = null
          }
          this.deleteForce = false
        })
      }
    }
  }
</script>

<style>
  .tag-title input {
    background: transparent!important;
    background-image: none;
    border: none;
    padding: 0;
    margin: 10px 0 7px;
    font-weight: bold;
    text-overflow: ellipsis;
    font-size: 1.5em;
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
  }
  .tag-title input:focus {
    text-decoration: underline;
    text-decoration-style: dashed;
    text-decoration-color: rgb(29, 140, 224);
  }
  .actions li {
    float: left;
    margin-right: 20px;
    font-size: 13px;
  }
  .actions li, .actions li a {
    color: #20a0ff;
    cursor: pointer;
    text-decoration: none;
  }
  .actions li:hover, .actions li a:hover {
    color: rgba(32, 157, 255, 0.64);
  }
  .actions {
    padding: 0;
    display: inline-block;
    list-style-type: none;
    margin: 0;
  }
</style>
