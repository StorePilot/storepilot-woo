<template>
  <td>
    <prop-switch
      :autosave="$sp.data.autosave"
      :prop="product.featured">
    </prop-switch>
  </td>
</template>

<script>
  import PropSwitch from '../../../../../components/properties/PropSwitch'
  export default {
    props: [
      'product'
    ],
    name: 'Featured',
    components: {
      PropSwitch
    }
  }
</script>
