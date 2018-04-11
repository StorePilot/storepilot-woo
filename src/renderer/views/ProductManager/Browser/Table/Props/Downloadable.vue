<template>
  <td>
    <prop-switch
      :autosave="$sp.data.autosave"
      :prop="product.downloadable">
    </prop-switch>
  </td>
</template>

<script>
  import PropSwitch from '../../../../../components/properties/PropSwitch'
  export default {
    props: [
      'product'
    ],
    name: 'Downloadable',
    components: {
      PropSwitch
    }
  }
</script>
