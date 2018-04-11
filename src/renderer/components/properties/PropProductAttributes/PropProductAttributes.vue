<template>
  <div class="sp-attributes-editor">
    <el-button style="display: block;" type="text" @click="visible = true">
      {{$sp.print['Edit']}}
    </el-button>
    <el-dialog :title="$sp.print['AttributesEditor']" :width="'90%'" :visible.sync="visible">
      <el-row :gutter="10">
        <el-col :sm="14" :lg="14" class="sp-global-attributes">
          <el-col :sm="12" :lg="12">
            <el-col :xs="24" :lg="24">
              <h4>{{$sp.print['GlobalAttributes']}}</h4>
              <div class="sp-list" v-loading="attributes.loading">
                <draggable @end="fixProductAttributes" :options="{sort: false, scroll: true, group: {name: 'attributes', pull: false, put: false}}">
                  <div
                    class="sp-list-item"
                    :class="{ selected: attribute === attr }"
                    @click="switchAttr(attr)"
                    v-loading="attr.loading"
                    v-for="attr in attributes.children">
                    <span>{{(attr.name.value === null || attr.name.value === '') ? $sp.print['Attribute'] : attr.name.value}}</span>
                    <div style="float: right" v-if="attr.id.value !== null">
                      <span>#{{attr.id.value}}</span>
                    </div>
                  </div>
                </draggable>
              </div>
              <el-button
                class="sp-margin-bottom"
                icon="el-icon-plus"
                @click="
                  attributes.children.push(new $sp.models.ProductAttribute($sp.controller));
                  attribute = attributes.children[(attributes.children.length - 1)]"
                :size="'mini'">
              </el-button>
            </el-col>
            <el-col :xs="24" :lg="24">
              <div v-loading="attribute.loading" v-if="attribute !== null">
                <editor-global-attribute :attribute="attribute"></editor-global-attribute>
                <el-button-group class="sp-margin-top">
                  <el-button @click="saveAttribute(attribute)" size="mini">{{$sp.print['Save']}}</el-button>
                  <el-button
                    @click="
                      remove(attribute, attributes);
                      terms.children = [];
                      terms.parent_id.value = null;
                      term = null;
                      attribute = null"
                    size="mini">{{$sp.print['Delete']}}</el-button>
                  <el-button @click="attributes.fetch()" size="mini" icon="el-icon-refresh"></el-button>
                </el-button-group>
              </div>
            </el-col>
          </el-col>
          <el-col :sm="12" :lg="12">
            <el-col :xs="24" :lg="24">
              <h4>{{$sp.print['Terms']}} {{(attribute !== null && attribute.id.value !== null) ? ($sp.print['for'] + ' "' + attribute.name.value + '"') : ''}}</h4>
              <div class="sp-list" v-loading="terms.loading">
                <div
                  @click="term = trm"
                  class="sp-list-item"
                  :class="{ selected: term === trm }"
                  v-loading="trm.loading"
                  v-for="trm in terms.children">
                  <span>{{(trm.name.value === null || trm.name.value === '') ? $sp.print['Term'] : trm.name.value}}</span>
                </div>
                <div class="sp-notify" v-if="attribute === null">
                  <span>{{$sp.print['SelectGlobalAttribute']}}</span>
                </div>
                <div class="sp-notify" v-else-if="attribute.id.value === null">
                  <span>{{$sp.print['SaveGlobalAttributeBeforeAddingTerms']}}</span>
                </div>
              </div>
              <el-button
                @click="addTerm(attribute.id.value); term = terms.children[(terms.children.length - 1)]"
                class="sp-margin-bottom"
                icon="el-icon-plus"
                :disabled="(attribute === null || attribute.id.value === null)"
                :size="'mini'">
              </el-button>
            </el-col>
            <el-col :xs="24" :lg="24">
              <div v-if="term !== null">
                <editor-term :term="term"></editor-term>
                <el-button-group class="sp-margin-top">
                  <el-button @click="term.save()" size="mini">{{$sp.print['Save']}}</el-button>
                  <el-button @click="remove(term, terms)" size="mini">{{$sp.print['Delete']}}</el-button>
                  <el-button @click="terms.fetch()" size="mini" icon="el-icon-refresh"></el-button>
                </el-button-group>
              </div>
            </el-col>
          </el-col>
        </el-col>
        <el-col :sm="10" :lg="10">
          <el-col :xs="24" :lg="24">
            <h2>{{$sp.print['ProductAttributes']}}</h2>
            <div
              v-loading="prop.loading"
              id="sp-product-attributes"
              class="sp-list sp-list-last"
              :class="{ 'sp-dragover': (dragover && !sort) }"
              @drop="drop = true; dragover = false"
              @dragleave="dragover = false"
              @dragover="$event.preventDefault(); dragover = true"
              @dragenter="dragover = true">
              <!-- @todo - Make sortable and update position on sort -->
              <draggable
                v-model="productAttributes"
                @start="sort = true"
                @end="drop = false; sort = false"
                :options="{ sort: false, scroll: true, group: {name: 'attributes', pull: false, put: 'attributes'} }">
                <div
                  v-loading="attr.loading"
                  v-for="attr in productAttributes"
                  class="sp-list-item"
                  :class="{ selected: productAttribute === attr }"
                  @dragover="dragover = true"
                  @dragenter="dragover = true"
                  @click="productAttribute = attr">
                  <span>{{(attr.name === null || attr.name === '') ? $sp.print['Attribute'] : attr.name}}</span>
                  <div
                    class="sp-attribute-tags-wrapper"
                    v-if="
                      product !== null &&
                      typeof product !== 'undefined' &&
                      product.type.value === 'variable' &&
                      productAttribute !== null &&
                      typeof variableAttr !== 'undefined' &&
                      productAttribute.variation">
                    <el-tag
                      size="mini"
                      v-for="term in getTerm(attr, variableAttr)"
                      :key="term.option + variableAttr.length">
                      {{term.option}}
                    </el-tag>
                  </div>
                  <div
                    class="sp-attribute-tags-wrapper"
                    v-else-if="
                      product !== null &&
                      typeof product !== 'undefined' &&
                      product.type.value === 'variable' &&
                      productAttribute !== null &&
                      defaultAttr !== 'undefined' &&
                      productAttribute.variation">
                    <el-tag
                      size="mini"
                      v-for="term in getTerm(attr, defaultAttr)"
                      :key="term.option + defaultAttr.length">
                      {{term.option}}
                    </el-tag>
                  </div>
                  <div style="float: right" v-if="attr.id !== 0">
                    <span>#{{attr.id}}</span>
                  </div>
                </div>
              </draggable>
              <div
                @dragover="dragover = true"
                @dragenter="dragover = true"
                style="opacity: .5"
                class="sp-notify sp-list-item">
                <span>{{$sp.print['DropAttributesHere']}}</span>
              </div>
            </div>
            <el-button
              @click="addAttribute()"
              class="sp-margin-bottom"
              icon="el-icon-plus"
              :size="'mini'">
            </el-button>
          </el-col>
          <el-col :xs="24" :lg="24">
            <el-col :xs="24" :lg="12">
              <div v-if="productAttribute !== null">
                <editor-product-attribute :productAttribute="productAttribute">
                </editor-product-attribute>
                <el-button-group class="sp-margin-top">
                  <el-button @click="saveProp" size="mini" :disabled="productAttribute.options.length === 0">{{$sp.print['Save']}}</el-button>
                  <el-button @click="remove(productAttribute, prop.value); productAttribute = null; autosave ? saveProp() : ''" size="mini">{{$sp.print['Delete']}}</el-button>
                  <el-button @click="reloadProp" size="mini" icon="el-icon-refresh"></el-button>
                </el-button-group>
              </div>
            </el-col>
            <el-col :xs="24" :lg="12">
              <h4>{{$sp.print['Options']}}</h4>
              <div v-if="productAttribute !== null" class="sp-list sp-list-opts">
                <span class="sp-notify" v-if="productAttribute.options.length === 0">
                  {{$sp.print['MinimumOneOptionRequired']}}
                </span>
                <div
                  class="sp-list-item"
                  v-for="(option, index) in productAttribute.options">
                  <span>{{option}}</span>
                  <el-button-group class="sp-list-item-btns">
                    <el-button size="mini" @click="productAttribute.options.splice(index, 1)">{{$sp.print['Delete']}}</el-button>
                    <el-button
                      size="mini"
                      v-if="
                        product !== null &&
                        typeof product !== 'undefined' &&
                        product.type.value === 'variable' &&
                        productAttribute !== null &&
                        productAttribute.id !== 0 &&
                        typeof defaultAttr !== 'undefined' &&
                        productAttribute.variation"
                      @click="setTerm(productAttribute, defaultAttr, option)">{{$sp.print['Default']}}</el-button>
                    <el-button
                      size="mini"
                      v-if="
                        product !== null &&
                        typeof product !== 'undefined' &&
                        product.type.value === 'variable' &&
                        productAttribute !== null &&
                        productAttribute.id !== 0 &&
                        typeof variableAttr !== 'undefined' &&
                        productAttribute.variation"
                      @click="setTerm(productAttribute, variableAttr, option)">{{$sp.print['Variation']}}</el-button>
                  </el-button-group>
                </div>
              </div>
              <el-autocomplete
                v-model="newOption"
                :size="'mini'"
                :disabled="productAttribute === null"
                :fetch-suggestions="termSuggestions"
                @select="addOption(productAttribute, $event.value)"
                style="width: calc(100% - 9px)"
                class="sp-margin-top"
                :placeholder="$sp.print['Option']">
                <el-button
                  @click="addOption(productAttribute, newOption)"
                  icon="el-icon-check"
                  slot="append"></el-button>
              </el-autocomplete>
            </el-col>
          </el-col>
        </el-col>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
  import draggable from 'vuedraggable'
  import EditorProductAttribute from './EditorProductAttribute'
  import EditorGlobalAttribute from './EditorGlobalAttribute'
  import EditorTerm from './EditorTerm'
  export default {
    components: {
      draggable,
      EditorProductAttribute,
      EditorGlobalAttribute,
      EditorTerm
    },
    props: [
      'prop',
      'defaultAttr',
      'variableAttr',
      'attributes',
      'terms',
      'autosave',
      'product'
    ],
    name: 'prop-product-attributes',
    data () {
      return {
        visible: false,
        productAttribute: null,
        attribute: null,
        term: null,
        newOption: '',
        drop: false,
        dragover: false,
        sort: false
      }
    },
    computed: {
      productAttributes () {
        return this.prop.value === null ? [] : this.prop.value
      }
    },
    methods: {
      fixProductAttributes (e) {
        if (this.drop) {
          let attr = this.attributes.children[e.oldIndex]
          let terms = this.terms.clone()
          let options = []
          terms.parent_id.value = attr.id.value
          terms.fetch().then(terms => {
            terms.children.forEach(term => { options.push(term.name.value) })
            this.prop.value.push({
              id: attr.id.value,
              name: attr.name.value,
              options: options
            })
            if (this.autosave) { this.prop.save() }
          })
        }
        this.drop = false
      },
      termSuggestions (queryString, cb) {
        let suggestions = []
        if (this.productAttribute !== null && this.productAttribute.id !== 0) {
          let terms = this.terms.clone()
          terms.parent_id.value = this.productAttribute.id
          terms.fetch().then(terms => {
            terms.children.forEach(term => {
              if (typeof this.productAttribute.options.find(opt => opt === term.name.value) === 'undefined') {
                suggestions.push({ value: term.name.value })
              }
            })
            cb(suggestions)
          })
        } else { cb(suggestions) }
      },
      switchAttr (attr) {
        this.attribute = attr
        if (this.attribute.id.value !== null) {
          this.terms.parent_id.value = this.attribute.id.value
          this.terms.fetch()
        } else {
          this.terms.parent_id.value = null
          this.terms.children = []
        }
        this.term = null
      },
      remove (attr, list) {
        if (attr.id !== null && typeof attr.id.value !== 'undefined' && attr.id.value !== null) {
          attr.remove(null, [{key: 'force', value: true}]).then(() => {
            let i = 0
            list.children.forEach(att => {
              if (att === attr) { list.children.splice(i, 1) } i++
            })
            attr = null
          })
        } else {
          let i = 0
          let l = list
          if (typeof list.children !== 'undefined') { l = list.children }
          l.forEach(att => { if (att === attr) { l.splice(i, 1) } i++ })
          attr = null
        }
      },
      addOption (attr, option) {
        attr.options.push(option)
        this.newOption = ''
        if (this.autosave) { this.prop.save() }
      },
      saveProp () {
        this.prop.save().then(() => {
          if (this.productAttribute !== null) {
            let id = this.productAttribute.id
            this.productAttribute.id = null
            this.prop.value.forEach(attr => {
              if (String(attr.id) === String(id)) { this.productAttribute = attr }
            })
          }
        })
        if (typeof this.defaultAttr !== 'undefined') { this.defaultAttr.save() }
        if (typeof this.variableAttr !== 'undefined') { this.variableAttr.save() }
      },
      reloadProp () {
        this.prop.fetch()
        if (typeof this.defaultAttr !== 'undefined') { this.defaultAttr.fetch() }
        if (typeof this.variableAttr !== 'undefined') { this.variableAttr.fetch() }
      },
      saveAttribute (attr) {
        let id = attr.id.value
        attr.save().then(() => {
          if (id === null) {
            this.terms.parent_id.value = this.attribute.id.value
            this.terms.fetch()
          }
        })
      },
      addAttribute (id = 0) {
        let attr = { id: id, name: '', position: 0, visible: true, variation: false, options: [] }
        if (this.prop.value === null) { this.prop.value = [attr] } else { this.prop.value.push(attr) }
        this.productAttribute = this.prop.value[(this.prop.value.length - 1)]
        if (this.autosave) { this.prop.save() }
      },
      addTerm (id) {
        let term = new this.$sp.models.ProductAttributeTerm(this.$sp.controller)
        term.parent_id.value = id
        this.terms.children.push(term)
      },
      getTerm (attr, atts) {
        let terms = []
        if (typeof atts !== 'undefined') {
          atts.value.forEach(att => { if (att.id === attr.id && att.name === attr.name) { terms.push(att) } })
        }
        return terms
      },
      setTerm (attr, atts, opt) {
        let attribute = atts.value.find(att => (att.id === attr.id && att.name === attr.name))
        let newAtts = []
        atts.value.forEach(att => { if (att !== attribute) { newAtts.push(att) } })
        atts.value = newAtts
        if (typeof attribute === 'undefined' || attribute.option !== opt) {
          if (atts.value.constructor !== Array) { atts.value = [] }
          atts.value.push({ id: attr.id, name: attr.name, option: opt })
        }
        if (this.autosave) { atts.save() }
      }
    }
  }
</script>

<style>
  .sp-attributes-editor h4 {
    margin: 0;
    padding: 5px 0;
  }
  .sp-attributes-editor table {
    width: calc(100% - 20px);
  }
  .sp-attributes-editor .sp-margin-bottom {
    margin-bottom: 10px;
  }
  .sp-attributes-editor .sp-margin-top {
    margin-top: 10px;
  }
  .sp-attributes-editor .sp-dragover * {
    pointer-events: none;
  }
  .sp-attribute-tags-wrapper {
    display: inline-block;
  }
  .sp-attribute-tags-wrapper > * {
    margin-right: 5px;
  }
  .sp-global-attributes {
    border: 1px dashed #ddd;
    padding: 10px;
  }
  .sp-list {
    margin-bottom: 10px;
    height: 130px;
    overflow-y: scroll;
  }
  .sp-list-last, .sp-list-opts {
    width: calc(100% + 20px);
  }
  .sp-list .sp-list-item {
    position: relative;
  }
  .sp-list .sp-list-item {
    font-size: .9em;
    cursor: pointer;
    width: calc(100% - 40px);
    padding: 5px 10px;
    border-radius: 3px;
  }
  .sp-list .sp-list-item:hover {
    background: #f5f7fa;
  }
  .sp-list .sp-list-item.selected {
    background: #ddeefc;
  }
  .sp-list-opts > .sp-list-item {
    padding: 8px 10px;
  }
  .sp-list-item-btns {
    margin-top: -2px;
    float: right;
  }
</style>
