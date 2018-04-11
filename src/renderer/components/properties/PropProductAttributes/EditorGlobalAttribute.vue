<template>
  <table>
    <tr>
      <td>{{$sp.print['Name']}}</td>
      <td>
        <el-input
          :placeholder="$sp.print['Name']"
          size="mini"
          v-model="attribute.name.value"></el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Slug']}}</td>
      <td>
        <el-input
          :placeholder="$sp.print['Slug']"
          size="mini"
          v-model="attribute.slug.value"></el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Type']}}</td>
      <td>
        <el-select size="mini" v-model="attribute.type.value" :placeholder="$sp.print['Type']">
          <el-option
            :label="$sp.print['Text']"
            value="text">
          </el-option>
          <el-option
            :label="$sp.print['Select']"
            value="select">
          </el-option>
        </el-select>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['OrderBy']}}</td>
      <td>
        <el-select size="mini" v-model="attribute.order_by.value" :placeholder="$sp.print['OrderBy']">
          <el-option
            :label="$sp.print['MenuOrder']"
            value="menu_order">
          </el-option>
          <el-option
            :label="$sp.print['Name']"
            value="name">
          </el-option>
          <el-option
            :label="$sp.print['Number']"
            value="name_num">
          </el-option>
          <el-option
            :label="$sp.print['Id']"
            value="id">
          </el-option>
        </el-select>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Archives']}}</td>
      <td>
        <el-switch v-model="attribute.has_archives.value"></el-switch>
      </td>
    </tr>
  </table>
</template>

<script>
  export default {
    props: [
      'attribute'
    ],
    name: 'EditorGlobalAttribute'
  }
</script>
