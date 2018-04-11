<template>
  <div v-loading="!fetched">
    <el-button
        style="display: block;"
        type="text"
        @click="reviewModal = true">
      {{$sp.print['View']}}
    </el-button>
    <el-dialog
        v-if="fetched"
        :title="$sp.print['Reviews']"
        :visible.sync="reviewModal">
      <el-carousel
          :interval="10000"
          indicator-position="outside"
          height="300px">
        <el-carousel-item
            v-if="reviews.children.length === 0">
          {{$sp.print['NoReviewsYet']}}
        </el-carousel-item>
        <el-carousel-item
            v-for="review in reviews.children"
            :key="review.id.value">
          <el-card class="box-card" style="margin: auto; max-width: 80%; max-height: 90%; overflow: auto;">
            <div slot="header" class="clearfix">
              <span style="line-height: 36px;">
                <b>{{$sp.print['Name']}}:</b> {{review.name.value}}
              </span>
              <span style="line-height: 36px;">
                <b>{{$sp.print['Date']}}:</b> {{review.date_created.value.substring(0, 10)}}
              </span>
              <div style="float: right;">
                <el-rate
                    disabled
                    v-model="review.rating.value">
                </el-rate>
                <el-checkbox
                    style="margin-top: 10px; float: right;"
                    disabled
                    v-model="review.verified.value">
                  {{review.verified.value ? $sp.print['Bought'] : $sp.print['DidntBuy']}}
                </el-checkbox>
              </div>
              <a
                  style="display: block;"
                  :href="'mailto: ' + review.email.value"
                  target="_blank">
                {{review.email.value}}
              </a>
            </div>
            <p>
              {{review.review.value}}
            </p>
          </el-card>
        </el-carousel-item>
      </el-carousel>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    props: [
      'productId'
    ],
    name: 'prop-reviews',
    data () {
      return {
        reviewModal: false,
        fetched: false,
        reviews: new this.$pap.Endpoint('reviews', this.$pap.controller, null, { batch: 'batch' })
      }
    },
    watch: {
      productId (id) {
        this.fetched = false
        this.reviews = new this.$pap.Endpoint('reviews', this.$pap.controller, null, {
          parent_id: id,
          batch: 'batch'
        })
        this.reviews.fetch().then(() => {
          this.fetched = true
        })
      }
    },
    mounted () {
      this.reviews = new this.$pap.Endpoint('reviews', this.$pap.controller, null, {
        parent_id: this.productId,
        batch: 'batch'
      })
      this.reviews.fetch().then(() => {
        this.fetched = true
      })
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
