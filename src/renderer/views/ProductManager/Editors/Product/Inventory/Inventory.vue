<template>
  <table
    cellspacing="0"
    class="sp-options">
    <tbody>
    <h3>{{$sp.print['General']}}</h3>
    <tr>
      <td>{{$sp.print['Rack']}}</td>
      <td>
        <el-input
          :placeholder="$sp.print['Rack']"
          v-model="rack.value"
          v-loading="product.meta_data.loading"
          @blur="($sp.data.autosave && product.meta_data.changed()) ? product.meta_data.save() : ''">
        </el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Barcode']}}</td>
      <td>
        <el-input
          :placeholder="$sp.print['Barcode']"
          v-model="barcode.value"
          v-loading="product.meta_data.loading"
          @blur="($sp.data.autosave && product.meta_data.changed()) ? product.meta_data.save() : ''">
        </el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['StockKeepingUnit']}}</td>
      <td>
        <el-input
          type="text"
          v-loading="product.sku.loading"
          v-model="product.sku.value"
          @blur="$sp.data.autosave ? product.sku.save().catch(
            function (e) {
              $message({
                message: $sp.print['InvalidOrDuplicatedSKU'],
                type: 'error'
              })
            }
           ) : ''"
          :placeholder="$sp.print['SKU']">
        </el-input>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['Featured']}}</td>
      <td>
        <prop-switch
          :prop="product.featured"
          :autosave="$sp.data.autosave"
        ></prop-switch>
      </td>
    </tr>
    <tr v-if="product.virtual.value !== null && product.type.value === 'simple'">
      <td>{{$sp.print['Virtual']}}</td>
      <td>
        <prop-switch
          :prop="product.virtual"
          :autosave="$sp.data.autosave"
        ></prop-switch>
      </td>
    </tr>
    <tr>
      <td>{{$sp.print['ManageStock']}}</td>
      <td>
        <el-checkbox
          style="margin: 20px 0 10px; width: 100%;"
          v-loading="product.manage_stock.loading"
          v-model="product.manage_stock.value"
          @change="$sp.data.autosave ? product.manage_stock.save() : ''">
          <div style="white-space: normal; position: absolute; top: 0; right: 0; width: 70%;">
            <i>Enable stock management at product level</i>
          </div>
        </el-checkbox>
      </td>
    </tr>
    <tr v-if="product.manage_stock.value">
      <td>{{$sp.print['StockQuantity']}}</td>
      <td>
        <prop-number
          :prop="product.stock_quantity"
          :autosave="$sp.data.autosave"
          :placeholder="$sp.print['StockQuantity']"
          style="margin: 20px 0 10px; width: 100%;"
        ></prop-number>
      </td>
    </tr>
    <tr v-if="product.manage_stock.value">
      <td>{{$sp.print['AllowBackorders']}}</td>
      <td>
        <el-select
          style="width: 100%; margin: 20px 0 10px;"
          v-model="product.backorders.value"
          v-loading="product.backorders.loading"
          :default-first-option="true"
          @change="$sp.data.autosave ? product.backorders.save() : ''">
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
      <td>{{$sp.print['SoldIndividually']}}</td>
      <td>
        <el-checkbox
          @change="$sp.data.autosave ? product.sold_individually.save() : ''"
          v-loading="product.sold_individually.loading"
          style="margin: 20px 0 10px; width: 100%;"
          v-model="product.sold_individually.value">
          <div style="white-space: normal; position: absolute; top: 0; right: 0; width: 70%;">
            <i>Enable this to only allow one of this item to be bought in a single order</i>                  </div>
        </el-checkbox>
      </td>
    </tr>
    </tbody>
    <tbody v-if="product.downloadable.value !== null && product.type.value === 'simple'">
    <h3>{{$sp.print['Downloadable']}}</h3>
    <tr>
      <td>{{$sp.print['Downloadable']}}</td>
      <td>
        <prop-switch
          :prop="product.downloadable"
          :autosave="$sp.data.autosave"
        ></prop-switch>
      </td>
    </tr>
    <tr v-if="product.downloadable.value">
      <td>{{$sp.print['DownloadableFiles']}}</td>
      <td>
      </td>
    </tr>
    <tr v-if="product.downloadable.value">
      <td colspan="2">
        <prop-downloads
          :prop="product.downloads"
          :autosave="$sp.data.autosave"
        ></prop-downloads>
      </td>
    </tr>
    <tr v-if="product.downloadable.value">
      <td>{{$sp.print['DownloadLimit']}}</td>
      <td>
        <prop-number
          :prop="product.download_limit"
          :autosave="$sp.data.autosave"
        ></prop-number>
      </td>
    </tr>
    <tr v-if="product.downloadable.value">
      <td>{{$sp.print['DownloadExpiry']}}</td>
      <td>
        <prop-number
          :prop="product.download_expiry"
          :autosave="$sp.data.autosave"
        ></prop-number>
      </td>
    </tr>
    </tbody>
    <tbody v-if="product.type.value !== 'external' && !product.virtual.value">
    <h3>{{$sp.print['Shipping']}}</h3>
    <tr>
      <td>{{$sp.print['Weight']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? product.weight.save() : ''"
          v-model="product.weight.value"
          v-loading="product.weight.loading"
          :placeholder="$sp.print['Weight']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="product.dimensions.value !== null">
      <td>{{$sp.print['Width']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? product.dimensions.save() : ''"
          v-model="product.dimensions.value.width"
          v-loading="product.dimensions.loading"
          :placeholder="$sp.print['Width']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="product.dimensions.value !== null">
      <td>{{$sp.print['Height']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? product.dimensions.save() : ''"
          v-model="product.dimensions.value.height"
          v-loading="product.dimensions.loading"
          :placeholder="$sp.print['Height']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    <tr v-if="product.dimensions.value !== null">
      <td>{{$sp.print['Length']}}</td>
      <td>
        <el-input
          type="text"
          @blur="$sp.data.autosave ? product.dimensions.save() : ''"
          v-model="product.dimensions.value.length"
          v-loading="product.dimensions.loading"
          :placeholder="$sp.print['Length']"
          style="margin: 0 0 10px; width: 100%;">
        </el-input>
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
  import PropSwitch from '../../../../../components/properties/PropSwitch'
  import PropDownloads from '../../../../../components/properties/PropDownloads'
  import PropNumber from '../../../../../components/properties/PropNumber'
  export default {
    props: [
      'product'
    ],
    name: 'Inventory',
    components: {
      PropSwitch,
      PropDownloads,
      PropNumber
    },
    computed: {
      rack () {
        let key = 'sp_rack'
        if (this.product.meta_data.value === null || this.product.meta_data.value.constructor !== Array) {
          this.product.meta_data.value = []
        }
        let val = this.product.meta_data.value.find(m => m.key === key)
        if (typeof val === 'undefined') {
          val = { key: key, value: '' }
          this.product.meta_data.value.push(val)
          this.product.meta_data.changed(false)
        }
        return val
      },
      barcode () {
        let key = 'sp_barcode'
        if (this.product.meta_data.value === null || this.product.meta_data.value.constructor !== Array) {
          this.product.meta_data.value = []
        }
        let val = this.product.meta_data.value.find(m => m.key === key)
        if (typeof val === 'undefined') {
          val = { key: key, value: '' }
          this.product.meta_data.value.push(val)
          this.product.meta_data.changed(false)
        }
        return val
      }
    }
  }
</script>
