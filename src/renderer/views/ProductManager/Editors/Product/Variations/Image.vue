<template>
  <tr>
    <td>{{$sp.print['Image']}}</td>
    <td>
      <prop-image
        :prop="variation.image"
        :autosave="$sp.data.autosave"
      ></prop-image>
    </td>
  </tr>
</template>

<script>
  import PropImage from '../../../../../components/properties/PropImage'
  export default {
    props: [
      'variation'
    ],
    name: 'ProductImage',
    components: {
      PropImage
    }
  }
</script>
