<template>
  <tr>
    <td>{{$sp.print['Description']}}</td>
    <td>
      <el-popover
        ref="popoverDescription"
        placement="left"
        trigger="click">
        <prop-textarea
          :raw="variation.description_raw"
          :prop="variation.description"
          :autosave="$sp.data.autosave"
        ></prop-textarea>
      </el-popover>
      <el-button
        v-popover:popoverDescription
        style="margin-left: 10px;"
        type="text">
        {{$sp.print['Edit']}}
      </el-button>
    </td>
  </tr>
</template>

<script>
  import propTextarea from '../../../../../components/properties/PropTextarea'
  export default {
    props: [
      'variation'
    ],
    name: 'Description',
    components: {
      propTextarea
    }
  }
</script>
