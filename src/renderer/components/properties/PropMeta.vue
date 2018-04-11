<template>
  <div>
    <el-button
        style="display: block;"
        type="text"
        @click="metaModal = true">
      {{$sp.print['Meta']}}
    </el-button>
    <span
        class="sp-meta"
        v-if="prop.value !== null"
        v-show="meta.value !== ''"
        v-for="meta in prop.value">
      {{meta.value}}
    </span>
    <el-dialog
        :title="$sp.print['MetaData']"
        :visible.sync="metaModal">
      <el-table
          v-loading="prop.loading || product.loading"
          :data="prop.value"
          style="width: 100%; margin-bottom: 10px;">
        <el-table-column
            prop="key"
            :label="$sp.print['Key']">
          <template slot-scope="scope">
            <el-input size="small" v-model="prop.value[scope.$index].key"></el-input>
          </template>
        </el-table-column>
        <el-table-column
            prop="value"
            :label="$sp.print['Value']">
          <template slot-scope="scope">
            <el-input size="small" v-model="prop.value[scope.$index].value"></el-input>
          </template>
        </el-table-column>
        <el-table-column
            :label="$sp.print['Actions']">
          <template slot-scope="scope">
            <el-button
                size="small"
                @click="prop.value[scope.$index].key = null; prop.value[scope.$index].value = null;">
              {{$sp.print['Clear']}}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-button
          @click="prop.value !== null ? prop.value.push({ key: '', value: ''}) : prop.value = [{ key: '', value: ''}]">
        {{$sp.print['New']}}
      </el-button>
      <el-button
          type="primary"
          @click="product.id.value !== null ? prop.save() : product.save()">
        {{$sp.print['Save']}}
      </el-button>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    props: [
      'prop',
      'product'
    ],
    name: 'prop-meta',
    data () {
      return {
        metaModal: false
      }
    }
  }
</script>

<style>
  .sp-meta {
    float: left;
    font-size: .7em;
    color: #fff;
    margin: 0 3px 3px 0;
    padding: 1px 3px;
    background: #7da3bd;
    border-radius: 5px;
  }
</style>
