<template>
  <table
    cellspacing="0"
    v-if="product !== null"
    class="sp-options">

    <!--GENERAL-->
    <tbody>

      <h3>General</h3>

      <product-slug :product="product"></product-slug>
      <product-gallery :product="product"></product-gallery>
      <product-status :product="product"></product-status>
      <product-type @goToVariations="$emit('goToVariations')" :product="product"></product-type>
      <product-price
        @toggleSale="cellSale=!cellSale"
        :saleIsScheduled="saleIsScheduled"
        :saleIsActive="saleIsActive"
        :product="product"></product-price>

    </tbody>

    <!--SALE SPOT-->
    <product-sale
      @scheduledSale="scheduledSale"
      @activeSale="activeSale"
      @clear="cellSale = false"
      :cellSale="cellSale"
      :product="product"></product-sale>

    <!--CONTINUE GENERAL-->
    <tbody style="width: 100%; border-left: 0;">

      <product-categories :product="product"></product-categories>
      <product-tags :product="product"></product-tags>
      <product-upsells :product="product" :data="data"></product-upsells>
      <product-cross-sells :product="product" :data="data"></product-cross-sells>

    </tbody>

    <!--ADDITIONAL-->
    <tbody>

      <h3>{{$sp.print['Additional']}}</h3>

      <product-attributes :product="product"></product-attributes>
      <!--product-meta :product="product"></product-meta-->
      <product-reviews :product="product"></product-reviews>

    </tbody>
  </table>
</template>

<script>
  import ProductSlug from './Slug'
  import ProductGallery from './Gallery'
  import ProductStatus from './Status'
  import ProductType from './Type'
  import ProductPrice from './Price'
  import ProductSale from './Sale'
  import ProductCategories from './Categories'
  import ProductTags from './Tags'
  import ProductUpsells from './Upsells'
  import ProductCrossSells from './CrossSells'
  import ProductAttributes from './Attributes'
  // import ProductMeta from './Meta'
  import ProductReviews from './Reviews'
  export default {
    props: [
      'data',
      'product'
    ],
    name: 'Header',
    components: {
      ProductSlug,
      ProductGallery,
      ProductStatus,
      ProductType,
      ProductPrice,
      ProductSale,
      ProductCategories,
      ProductTags,
      ProductUpsells,
      ProductCrossSells,
      ProductAttributes,
      // ProductMeta,
      ProductReviews
    },
    data () {
      return {
        saleIsScheduled: false,
        saleIsActive: false,
        cellSale: false
      }
    },
    methods: {
      scheduledSale (val) {
        this.saleIsScheduled = val
      },
      activeSale (val) {
        this.saleIsActive = val
      }
    }
  }
</script>
