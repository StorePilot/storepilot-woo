<template>
  <div>
    <el-alert
        style="margin: 5px; width: calc(100% - 10px)"
        :closable="false"
        v-if="!activated.wc && mounted"
        :title="$sp.print['ActivateWoocommerce']"
        type="warning"
        show-icon>
      <el-button
          style="float: right; margin-left: 20px"
          @click="$router.push('/activate')"
          size="mini">
        {{$sp.print['Activate']}}
      </el-button>
    </el-alert>
    <el-alert
        style="margin: 5px; width: calc(100% - 10px)"
        :closable="false"
        v-if="!activated.pl && mounted"
        :title="$sp.print['EnablePrettyPermalinks']"
        type="warning"
        show-icon>
      <el-button
          style="float: right; margin-left: 20px"
          @click="$router.push('/permalinks')"
          size="mini">
        {{$sp.print['Enable']}}
      </el-button>
    </el-alert>
    <el-alert
        style="margin: 5px; width: calc(100% - 10px)"
        :closable="false"
        v-if="activated.wc && !authenticated && activated.pl && mounted"
        :title="$sp.print['AuthorizeStorepilot']"
        type="warning"
        show-icon>
      <span style="padding-left: 20px">(Dev)</span>
      <el-button
          style="float: right; margin-left: 20px"
          @click="$router.push('/authorize')"
          size="mini">
        {{$sp.print['Authorize']}}
      </el-button>
      <el-button
        style="float: right; margin-left: 20px"
        @click="$router.push('/activate')"
        size="mini">
        {{$sp.print['Activate']}}
      </el-button>
    </el-alert>
  </div>
</template>

<script>
  export default {
    name: 'warnings',
    props: [
      'activated',
      'authenticated',
      'mounted'
    ]
  }
</script>
