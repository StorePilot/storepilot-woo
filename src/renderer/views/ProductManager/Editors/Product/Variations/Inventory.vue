<template>
  <tbody>
    <h3>{{$sp.print['Inventory']}}</h3>
    <product-sku :variation="variation"></product-sku>
    <tr v-if="!(variation.manage_stock.value === 'parent' || product.manage_stock.value)">
      <td>{{$sp.print['ManageStock']}} {{product.manage_stock.value ? ' - (Hooked to Parent)' : ''}}</td>
      <td>
        <el-checkbox
          :disabled="variation.manage_stock.value === 'parent' || product.manage_stock.value"
          style="margin: 20px 0 10px; width: 100%;"
          v-model="variation.manage_stock.value"
          v-loading="variation.manage_stock.loading"
          @change="$sp.data.autosave ? variation.manage_stock.save() : ''">
          <div style="white-space: normal; position: absolute; top: 0; right: 0; width: 70%;">
            <i v-if="variation.manage_stock.value === 'parent' || product.manage_stock.value" style="display: block; font-size: .8em">(Requires stock management at product level to be disabled)</i>
            <i>Enable stock management at variation level</i>
          </div>
        </el-checkbox>
        <el-button
          :disabled="product.manage_stock.value"
          v-if="variation.manage_stock.value === 'parent'"
          @click="variation.manage_stock.value = false; $sp.data.autosave ? variation.manage_stock.save() : ''">
          Unhook from Parent
        </el-button>
      </td>
    </tr>
    <tr v-if="variation.manage_stock.value && variation.manage_stock.value !== 'parent'">
      <td>{{$sp.print['StockQuantity']}}</td>
      <td>
        <prop-number
          :prop="variation.stock_quantity"
          :autosave="$sp.data.autosave"
          :placeholder="$sp.print['StockQuantity']"
        ></prop-number>
      </td>
    </tr>
    <tr v-if="variation.manage_stock.value && variation.manage_stock.value !== 'parent'">
      <td>{{$sp.print['AllowBackorders']}}</td>
      <td>
        <el-select
          style="width: 100%; margin: 20px 0 10px;"
          v-model="variation.backorders.value"
          v-loading="variation.backorders.loading"
          :default-first-option="true"
          @change="$sp.data.autosave ? variation.backorders.save() : ''">
          <el-option
            :label="$sp.print['DoNotAllow']"
            value="no"></el-option>
          <el-option
            :label="$sp.print['AllowButNotifyCustomer']"
            value="notify"></el-option>
          <el-option
            :label="$sp.print['Allow']"
            value="yes"></el-option>
        </el-select>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Virtual']}}</td>
      <td>
        <prop-switch
          :prop="variation.virtual"
          :autosave="$sp.data.autosave"
        ></prop-switch>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Downloadable']}}</td>
      <td>
        <prop-switch
          :prop="variation.downloadable"
          :autosave="$sp.data.autosave"
        ></prop-switch>
      </td>
    </tr>
    <tr v-if="variation.downloadable.value">
      <td>{{$sp.print['DownloadableFiles']}}</td>
      <td>
        <el-popover
          width="400"
          ref="popoverDownloadables"
          placement="left"
          trigger="click">
          <prop-downloads
            :prop="variation.downloads"
            :autosave="$sp.data.autosave"
          ></prop-downloads>
        </el-popover>
        <el-button
          v-popover:popoverDownloadables
          style="margin-left: 10px;"
          type="text">
          {$sp.print['Edit']}}
        </el-button>
      </td>
    </tr>
    <tr v-if="variation.downloadable.value">
      <td>{{$sp.print['DownloadLimit']}}</td>
      <td>
        <prop-number
          :prop="variation.download_limit"
          :autosave="$sp.data.autosave"
        ></prop-number>
      </td>
    </tr>
    <tr v-if="variation.downloadable.value">
      <td>{{$sp.print['DownloadExpiry']}}</td>
      <td>
        <prop-number
          :prop="variation.download_expiry"
          :autosave="$sp.data.autosave"
        ></prop-number>
      </td>
    </tr>
  </tbody>
</template>

<script>
  import ProductSku from './Sku'
  import PropNumber from '../../../../../components/properties/PropNumber'
  import PropDownloads from '../../../../../components/properties/PropDownloads'
  import PropSwitch from '../../../../../components/properties/PropSwitch'
  export default {
    props: [
      'variation',
      'product'
    ],
    name: 'Inventory',
    components: {
      ProductSku,
      PropNumber,
      PropDownloads,
      PropSwitch
    }
  }
</script>
