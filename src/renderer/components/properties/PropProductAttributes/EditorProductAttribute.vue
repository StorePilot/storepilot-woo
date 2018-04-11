<template>
  <table>
    <tr>
      <td>{{$sp.print['Id']}}</td>
      <td>
        <el-input
          :placeholder="$sp.print['GlobaleAttributeIdOrZero']"
          size="mini"
          v-model="productAttribute.id"></el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Name']}}</td>
      <td>
        <el-input
          :disabled="String(productAttribute.id) !== '0'"
          :placeholder="$sp.print['Name']"
          size="mini"
          v-model="productAttribute.name"></el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Position']}}</td>
      <td>
        <el-input-number
          :placeholder="$sp.print['Position']"
          size="mini"
          v-model="productAttribute.position"></el-input-number>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Visible']}}</td>
      <td>
        <el-switch v-model="productAttribute.visible"></el-switch>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Variation']}}</td>
      <td>
        <el-switch v-model="productAttribute.variation"></el-switch>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Archives']}}</td>
      <td>
        <el-switch v-model="productAttribute.has_archives"></el-switch>
      </td>
    </tr>
  </table>
</template>

<script>
  export default {
    props: [
      'productAttribute'
    ],
    name: 'EditorProductAttribute'
  }
</script>
