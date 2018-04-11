<template>
  <div>
    <!-- HAS PARENT WARNING -->
    <el-alert
      class="sp-warning"
      v-if="product.parent_id.value !== 0"
      :title="$sp.print['ParentIdIsNotZero']"
      :closable="false"
      type="warning"
      show-icon>
      <p class="el-alert__description">{{$sp.print['MightBeHiddenForYourCustomers']}}</p>
      <el-button
        type="warning"
        @click="product.parent_id.value = 0; product.parent_id.save()"
        size="mini">
        {{$sp.print['ResetToZero']}}
      </el-button>
    </el-alert>

    <!-- TRASHED WARNING -->
    <el-alert
      class="sp-warning"
      v-if="product.status.value === 'trash'"
      :title="sp.print['Trashed']"
      type="warning"
      :description="sp.print['ThisProductIsTrashed']"
      :closable="false"
      show-icon>
    </el-alert>
  </div>
</template>

<script>
  export default {
    props: [
      'product'
    ],
    name: 'Warnings'
  }
</script>

<style>
  .el-alert__content {
    width: 100%;
  }
  .sp-warning {
    margin: 5px;
    width: calc(100% - 10px);
  }
  .sp-warning button {
    position: absolute;
    right: 5px;
    top: 10px;
  }
</style>
