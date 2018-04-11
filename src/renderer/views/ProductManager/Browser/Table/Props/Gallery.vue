<template>
  <td style="min-width: 200px!important">
    <div class="sp-table-gallery">
      <prop-gallery
        :size="'small'"
        :prop="product.images"
        :autosave="$sp.data.autosave">
      </prop-gallery>
    </div>
  </td>
</template>

<script>
  import propGallery from '../../../../../components/properties/PropGallery'
  export default {
    props: [
      'product'
    ],
    name: 'Gallery',
    components: {
      propGallery
    }
  }
</script>

<style>
  .sp-table-gallery {
    height: 40px;
    width: 100%;
    overflow-y: scroll;
  }
</style>
