<template>
  <div>
    <div style="margin-bottom: 10px;">
      <h3>{{$sp.print['SelectVariation']}}</h3>
      <div class="sp-variations-sortable">
        <div
            style="cursor: pointer; width: 70px; margin: 0 20px 20px 0; position: relative; float: left;"
            v-for="(variation, index) in variations.children"
            :class="{
              'sp-variation-selected': current !== null && current.id !== null && current.id.value === variation.id.value
            }"
            :data-id="variation.id.value"
            :key="variation.id.value"
            @click="current = variation">
          <div
              class="variation"
              :style="{background: (variation.image.value !== null) ? 'url(' + variation.image.value.src_shop_thumbnail + ')' : '#7da3bd'}">
            <div style="position: absolute; left: -5px; top: -5px;">
              <span
                  class="sp-attribute"
                  v-if="variation.attributes.value !== null"
                  v-show="attribute.option !== ''"
                  v-for="attribute in variation.attributes.value">
                {{attribute.option}}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <el-button
        style="margin-bottom: 20px; width: 100%;"
        @click="createVariation">
      + {{$sp.print['Variation']}}
    </el-button>

    <div style="border-bottom: 1px solid #d1dbe5"></div>

    <!-- Properties -->
    <table
        cellspacing="0"
        v-if="current !== null"
        style="width: 100%; border-collapse: collapse; margin-top: 20px"
        class="sp-options">
      <tbody style="width: 100%;">
        <h3>{{$sp.print['General']}}</h3>
        <product-attributes :variation="current" :product="product"></product-attributes>
        <product-image :variation="current"></product-image>
        <product-description :variation="current"></product-description>
        <product-price
          @toggleSale="cellSale=!cellSale"
          :saleIsScheduled="saleIsScheduled"
          :saleIsActive="saleIsActive"
          :variation="current"></product-price>
      </tbody>

      <!--SALE SPOT-->
      <product-sale
        @scheduledSale="scheduledSale"
        @activeSale="activeSale"
        @clear="cellSale = false"
        :cellSale="cellSale"
        :variation="current"></product-sale>

      <tbody>
        <product-visible :variation="current"></product-visible>
      </tbody>
      <product-inventory :variation="current" :product="product"></product-inventory>
      <product-shipping :variation="current"></product-shipping>
      <product-additional
        @deleteVariation="deleteVariation()"
        :variations="variations"
        :variation="current"></product-additional>
    </table>
  </div>
</template>

<script>
  import ProductPrice from './Price'
  import ProductSale from './Sale'
  import ProductDescription from './Description'
  import ProductAttributes from './Attributes'
  import ProductImage from './Image'
  import ProductVisible from './Visible'
  import ProductInventory from './Inventory'
  import ProductShipping from './Shipping'
  import ProductAdditional from './Additional'

  import gridProduct from '../../../../../components/Product'
  import propTitle from '../../../../../components/properties/PropTitle'
  export default {
    components: {
      ProductPrice,
      ProductSale,
      ProductDescription,
      ProductAttributes,
      ProductImage,
      ProductVisible,
      ProductInventory,
      ProductShipping,
      ProductAdditional,
      gridProduct,
      propTitle
    },
    props: [
      'product',
      'variationIds',
      'parentId',
      'changes'
    ],
    data () {
      return {
        cellSale: false,
        saleIsScheduled: false,
        saleIsActive: false,
        current: null,
        variations: new this.$pap.List(this.$sp.models.ProductVariation, this.$pap.controller, null, {
          parent_id: this.parentId,
          batch: 'batch'
        })
      }
    },
    created () {
      this.variations.fetch().then(() => {
        this.variations.sort()
      })
    },
    computed: {
      changesTotal () {
        let i = 0
        this.variations.children.forEach(model => {
          i += model.changes(false, true).length
        })
        return this.changes + i
      }
    },
    watch: {
      'changesTotal' (changes) {
        this.$sp.data.changes = changes
      },
      '$sp.data.on.save' () {
        this.variations.children.forEach(variation => {
          if (variation.attributes.value !== null) {
            variation.attributes.value.forEach(attribute => {
              delete attribute.options
            })
          }
        })
        this.variations.save().catch(e => {
        })
      },
      parentId (val) {
        this.variations.clear()
        this.$sp.data.changes = this.changesTotal
        this.variations.parent_id.value = val
        this.variations.fetch().then(variations => {
          this.variations.sort()
          if (variations.children.length > 0) {
            this.current = variations.children[0]
          } else {
            this.current = null
          }
        })
      }
    },
    methods: {
      scheduledSale (val) {
        this.saleIsScheduled = val
      },
      activeSale (val) {
        this.saleIsActive = val
      },
      createVariation () {
        let variation = new this.$sp.models.ProductVariation(this.$pap.controller)
        variation.parent_id.value = this.parentId
        this.variations.children.push(variation)
        if (this.$sp.data.autosave) {
          this.variations.save()
        }
      },
      deleteVariation () {
        if (this.current.id.value === null) {
          this.current = null
        } else {
          let i = 0
          let index = i
          this.variations.children.forEach(variation => {
            if (variation.id.value === this.current.id.value) {
              index = i
              variation.remove().then(() => {
                this.variations.children.splice(index, 1)
                this.current = null
                this.variationIds.fetch()
              })
            }
            i++
          })
        }
      },
      propertyArrayDelete (property, index) {
        this.product[property].value.splice(index, 1)
        if (this.$sp.data.autosave) {
          this.product[property].save()
        }
      }
    }
  }
</script>

<style>
  .sp-selection .el-select-dropdown__item.selected {
    color: #000!important;
    background-color: #fff!important;
  }
  .sp-selection .el-select-dropdown__item.selected.sp-selected {
    color: #fff!important;
    background-color: #20a0ff!important;
  }
  .sp-selection .el-select-dropdown__item.selected.sp-selected {
    background-color: #1c8de0!important;
  }
  .sp-selection .el-select-dropdown__item.hover,
  .sp-selection .el-select-dropdown__item:hover {
    background-color: #e4e8f1!important;
  }
  .sp-options td {
    padding-bottom: 25px;
  }
  .sp-options td:first-child {
    width: 35%;
  }
  .sp-options td:last-child {
    width: 65%;
  }
  .sp-options .price td:first-child {
    width: 35%;
  }
  .sp-options .price td:last-child {
    width: 65%;
  }
  .variation {
    width: 70px;
    height: 70px;
    background-size: cover!important;
  }
  .variation:hover {
    opacity: .7;
  }
  .sp-variation-selected .variation {
    width: 64px;
    height: 64px;
    border: 3px solid rgba(32, 157, 255, 0.64);
  }
  .sp-attribute-table td, th {
    width: 25%;
    text-align: left;
    padding: 10px;
  }
  .sp-attribute-table th {
    font-size: 1.1em;
    background-color: #eef1f6;
  }
  .sp-attribute {
    float: left;
    font-size: .7em;
    color: #fff;
    margin: 0 3px 3px 0;
    padding: 1px 3px;
    background: #7da3bd;
    border-radius: 5px;
  }
</style>
