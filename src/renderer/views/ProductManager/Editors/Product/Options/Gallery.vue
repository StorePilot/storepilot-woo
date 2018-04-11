s<template>
  <tr>
    <td>{{$sp.print['Gallery']}}</td>
    <td>
      <prop-gallery
        :prop="product.images"
        :autosave="$sp.data.autosave">
      </prop-gallery>
    </td>
  </tr>
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
