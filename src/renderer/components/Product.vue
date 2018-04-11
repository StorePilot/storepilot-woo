<template>
  <div
      style="outline: none"
      tabindex="0"
      :style="typeof product === 'undefined' || product.name.value === 'Unresolved' ? 'background: #ff9c9c' : ''"
      :data-item-id="typeof product !== 'undefined' ? product.id.value : 0"
      data-item-type="product"
      class="product"
      v-loading="typeof product !== 'undefined' ? product.loading : false">
    <div
        v-if="typeof product !== 'undefined' && product.images.value !== 0 && product.images.value !== null && typeof product.images.value[0] !== 'undefined'"
        class="image"
        v-lazy:background-image="product.images.value[0].src_shop_thumbnail">
    </div>
    <div
        v-else
        class="image">
    </div>
    <div class="sp-status-label" v-if="typeof product !== 'undefined' && product.status.value !== 'publish'">
      <span>{{product.status.value.charAt(0).toUpperCase() + product.status.value.slice(1)}}</span>
    </div>
    <div
        class="sp-sale-label"
        v-if="typeof product !== 'undefined' && product.on_sale.value"
        type="primary">
      {{saleLabelFromMeta(product.meta_data.value)}}
    </div>
    <div class="title">
      <div class="sp-product-rating" v-if="rate">
        <el-rate
            disabled
            class="sp-rating"
            v-model="rate">
        </el-rate>
      </div>

      <span class="name" v-if="typeof product === 'undefined' || product.name.value === 'Unresolved'">
        {{$sp.print['Missing']}}<br>
        #{{typeof product === 'undefined' ? 'Undefined' : product.id.value}}
      </span>
      <span class="name" v-else>
        {{product.name.value}}
      </span>
      <span class="price" v-if="typeof product !== 'undefined' && product.price_range.value !== null">
        <span :class="(product.on_sale.value && product.sale_price.value !== null) ? 'sp-price-sale' : 'sp-price-value'">
          {{(product.price_range.value.min === product.price_range.value.max) ? ((product.regular_price.value !== '') ? product.regular_price.value : 0) : (product.price_range.value.min + ' - ' + product.price_range.value.max)}}
          {{typeof currencies !== 'undefined' ? $sp.valuta.symbol(currencies.value.value) : ''}}
        </span>
        <span v-if="product.on_sale.value && product.type.value === 'simple' && product.sale_price.value !== null">
          {{product.sale_price.value !== '' ? product.sale_price.value : 0}}
          {{typeof currencies !== 'undefined' ? $sp.valuta.symbol(currencies.value.value) : ''}}
        </span>
      </span>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'product',
    props: [
      'product',
      'currencies'
    ],
    data () {
      return {
        rate: typeof this.product !== 'undefined' ? parseFloat(this.product.average_rating.value) : 0
      }
    },
    mounted () {
      this.rate = typeof this.product !== 'undefined' ? parseFloat(this.product.average_rating.value) : 0
    },
    watch: {
      'product.average_rating.value' (val) {
        this.rate = Number(val)
      }
    },
    methods: {
      saleLabelFromMeta (meta) {
        let label = ''
        if (meta.constructor === Array) {
          label = meta.find(m => m.key === 'sp_custom_sale_flash_label')
          label = typeof label !== 'undefined' ? label.value : ''
        }
        return label === '' ? this.$sp.print['Sale!'] : label
      }
    }
  }
</script>

<style>
  .sp-rating .el-rate__icon {
    font-size: 12px;
  }
  .sp-price-sale {
    text-decoration: line-through;
  }
  .sp-price-value, .sp-price-sale {
    margin-right: 10px;
  }
  .sp-status-label {
    position: absolute;
    background: rgba(0, 0, 0, .7);
    left: 0;
    top: 0;
    color: #fff;
    padding: 10px;
    font-size: .9em;
  }
  .sp-sale-label {
    position: absolute;
    right: -10px;
    top: -10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #20a0ff;
    color: #fff;
    border-radius: 50%;
    vertical-align: middle;
    padding: 3px;
    font-size: .9em;
    z-index: 1000;
  }
  .sp-sale-label:before {
    content: '';
    float: left;
    width: auto;
    padding-bottom: 100%;
  }
  .product {
    height: 100%;
    position: relative;
    border: 2px solid #f5f5f5;
    transition: border-color .2s, border-width .2s;
    border-radius: 3px;
  }
  .product:hover {
    cursor: pointer;
    border-color: #ccc;
  }
  .sp-product-selected .product {
    border-width: 3px;
    border-color: rgba(32, 157, 255, .5);
  }
  .sp-product-selected .product:focus {
    border-width: 4px;
    border-color: rgba(32, 157, 255, .64);
  }
  .product .title {
    position: absolute;
    bottom: 0px;
    width: 90%;
    padding: 2% 5% 5% 5%;
    background-color: rgba(255, 255, 255, .6)
  }

  .product .title .name {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: block;
    color: #555;
    font-weight: 500;
    padding: 2px 0;
  }
  .product .title .price {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: block;
    font-size: .8em;
    font-weight: 500;
    color: #777;
  }
  .product .title .price span {
    display: inline-block;
  }
  .product .image {
    color: #99A9BF;
    display: block;
    border:0;
    padding-top: 100%;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
  }
</style>
