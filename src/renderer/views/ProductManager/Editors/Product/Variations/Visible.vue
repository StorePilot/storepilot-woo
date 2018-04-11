<template>
  <tr>
    <td>{{$sp.print['Visible']}}</td>
    <td>
      <prop-switch
        :prop="variation.visible"
        :autosave="$sp.data.autosave"
      ></prop-switch>
    </td>
  </tr>
</template>

<script>
  import PropSwitch from '../../../../../components/properties/PropSwitch'
  export default {
    props: [
      'variation'
    ],
    name: 'Visible',
    components: {
      PropSwitch
    }
  }
</script>
