<template>
  <tr>
    <td>{{$sp.print['StockKeepingUnit']}}</td>
    <td>
      <el-input
        v-loading="variation.sku.loading"
        @blur="$sp.data.autosave ? current.sku.save().catch(
          function (e) {
            $message({
              message: $sp.print['InvalidOrDuplicatedSKU'],
              type: 'error'
            })
          }
         ) : ''"
        type="text" v-model="variation.sku.value"
        :placeholder="$sp.print['SKU']">
      </el-input>
    </td>
  </tr>
</template>

<script>
  export default {
    props: [
      'variation'
    ],
    name: 'Sku'
  }
</script>
