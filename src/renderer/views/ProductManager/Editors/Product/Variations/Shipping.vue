<template>
  <tbody>
    <h3>{{$sp.print['Shipping']}}</h3>
    <tr>
      <td>{{$sp.print['Weight']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? variation.weight.save() : ''"
          v-model="variation.weight.value"
          v-loading="variation.weight.loading"
          :placeholder="$sp.print['Weight']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="variation.dimensions.value !== null">
      <td>{{$sp.print['Width']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? variation.dimensions.save() : ''"
          v-model="variation.dimensions.value.width"
          v-loading="variation.dimensions.loading"
          :placeholder="$sp.print['Width']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="variation.dimensions.value !== null">
      <td>{{$sp.print['Height']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? variation.dimensions.save() : ''"
          v-model="variation.dimensions.value.height"
          v-loading="variation.dimensions.loading"
          :placeholder="$sp.print['Height']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="variation.dimensions.value !== null">
      <td>{{$sp.print['Length']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? variation.dimensions.save() : ''"
          v-model="variation.dimensions.value.length"
          v-loading="variation.dimensions.loading"
          :placeholder="$sp.print['Length']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
  </tbody>
</template>

<script>
  export default {
    props: [
      'variation'
    ],
    name: 'Shipping'
  }
</script>
