<template>
  <draggable
    class="sp-nested-nav"
    @end="dropEnd"
    v-model="items"
    :options="dragOptions">
    <div
      v-for="item in items"
      :key="item.id.value">
      <el-menu-item
          v-if="item.children.length < 1"
          @click="itemClickInternal(item)"
          class="sp-menu-item"
          :index="item.id.value.toString()">
        <div
          @contextmenu.prevent="ctxOpen($event, item)"
          class="sp-cover"
          :id="item.id.value + '-sp-cover'"
          @dragleave="removeDragover(item.id.value + '-sp-cover')"
          @dragenter="addDragover(item.id.value + '-sp-cover')"
          @touchend="removeDragover(item.id.value + '-sp-cover')"
          @touchmove="addDragover(item.id.value + '-sp-cover')"
          @drop="dropInternal(item, 'cover'); removeDragover(item.id.value + '-sp-cover')"></div>
        <div
          @contextmenu.prevent="ctxOpen($event, item)"
          class="sp-top"
          :id="item.id.value + '-sp-top'"
          @dragleave="removeDragover(item.id.value + '-sp-top')"
          @dragenter="addDragover(item.id.value + '-sp-top')"
          @touchend="removeDragover(item.id.value + '-sp-top')"
          @touchmove="addDragover(item.id.value + '-sp-top')"
          @drop="dropInternal(item, 'top'); removeDragover(item.id.value + '-sp-top')"></div>
        <div
          @contextmenu.prevent="ctxOpen($event, item)"
          class="sp-bottom"
          :id="item.id.value + '-sp-bottom'"
          @dragleave="removeDragover(item.id.value + '-sp-bottom')"
          @dragenter="addDragover(item.id.value + '-sp-bottom')"
          @touchend="removeDragover(item.id.value + '-sp-bottom')"
          @touchmove="addDragover(item.id.value + '-sp-bottom')"
          @drop="dropInternal(item, 'bottom'); removeDragover(item.id.value + '-sp-bottom')"></div>
        <span class="sp-menu-item-label">{{item.name.value}}</span>
        <span class="sp-menu-item-count">{{item.count.value}}</span>
      </el-menu-item>
      <el-submenu v-else :index="item.id.value.toString()">
        <template slot="title">
          <el-menu-item
              @click="itemClickInternal(item)"
              class="sp-menu-item title"
              :index="item.id.value.toString()">
            <div
              @contextmenu.prevent="ctxOpen($event, item)"
              class="sp-cover"
              :id="item.id.value + '-sp-cover'"
              @dragleave="removeDragover(item.id.value + '-sp-cover')"
              @dragenter="addDragover(item.id.value + '-sp-cover')"
              @touchend="removeDragover(item.id.value + '-sp-cover')"
              @touchmove="addDragover(item.id.value + '-sp-cover')"
              @drop="dropInternal(item, 'cover', true); removeDragover(item.id.value + '-sp-cover')"></div>
            <div
              @contextmenu.prevent="ctxOpen($event, item)"
              class="sp-top"
              :id="item.id.value + '-sp-top'"
              @dragleave="removeDragover(item.id.value + '-sp-top')"
              @dragenter="addDragover(item.id.value + '-sp-top')"
              @touchend="removeDragover(item.id.value + '-sp-top')"
              @touchmove="addDragover(item.id.value + '-sp-top')"
              @drop="dropInternal(item, 'top', true); removeDragover(item.id.value + '-sp-top')"></div>
            <div
              @contextmenu.prevent="ctxOpen($event, item)"
              class="sp-bottom"
              :id="item.id.value + '-sp-bottom'"
              @dragleave="removeDragover(item.id.value + '-sp-bottom')"
              @dragenter="addDragover(item.id.value + '-sp-bottom')"
              @touchend="removeDragover(item.id.value + '-sp-bottom')"
              @touchmove="addDragover(item.id.value + '-sp-bottom')"
              @drop="dropInternal(item, 'bottom', true); removeDragover(item.id.value + '-sp-bottom')"></div>
            <span class="sp-menu-item-label">{{item.name.value}}</span>
            <span class="sp-menu-item-count">{{item.count.value}}</span>
          </el-menu-item>
        </template>
        <nested-menu
            @item-drop-internal="itemDropInternal"
            @item-click-internal="itemClickInternal"
            :items="item.children"
            :shared="sharedInternal"
            :ctx="ctx"
            :refs="refs">
        </nested-menu>
      </el-submenu>
    </div>
  </draggable>
</template>

<script>
  import draggable from 'vuedraggable'
  export default {
    name: 'nested-menu',
    components: {
      draggable
    },
    props: [
      'items',
      'ctx',
      'refs',
      'shared'
    ],
    created () {
      if (typeof this.shared !== 'undefined') {
        this.root = false
        this.sharedInternal = this.shared
      } else {
        document.addEventListener('sp-drop-product', event => {
          let e = event.detail
          let item = null
          if (typeof e.to.__vue__.value !== 'undefined' && e.to.__vue__.value.length > e.newIndex) {
            item = e.to.__vue__.value[e.newIndex]
          }
          this.sharedInternal.dropItem = {
            type: 'product',
            item: item,
            index: e.newIndex,
            preIndex: e.oldIndex,
            parent: e.to.__vue__.value,
            event: e
          }
        })
        document.addEventListener('sp-drop-category', event => {
          let e = event.detail
          let item = null
          if (typeof e.from.__vue__.value !== 'undefined' && e.from.__vue__.value.length > e.oldIndex) {
            item = e.from.__vue__.value[e.oldIndex]
          } else if (typeof e.from.__vue__.items !== 'undefined' && e.from.__vue__.items.length > e.oldIndex) {
            item = e.from.__vue__.items[e.oldIndex]
          }
          this.sharedInternal.dropItem = {
            type: 'category',
            item: item,
            index: e.oldIndex,
            preIndex: e.oldIndex, // @note - Index has not changed
            parent: e.from.__vue__.value,
            event: e
          }
        })
      }
    },
    data () {
      return {
        root: true,
        sharedInternal: {
          dropItem: null,
          targetItem: null
        },
        dragOptions: {
          sort: false,
          scroll: true,
          group: {
            name: 'category',
            pull: 'clone',
            put: false
          }
        }
      }
    },
    methods: {
      ctxOpen (e, item) {
        if (typeof this.ctx !== 'undefined' && this.ctx && typeof this.refs !== 'undefined') {
          this.refs.ctx.open(e, {type: 'category', item: item})
        }
      },
      addDragover (id) {
        document.getElementById(id).classList.add('sp-dragover')
      },
      removeDragover (id) {
        document.getElementById(id).classList.remove('sp-dragover')
      },
      dropInternal (item, area = 'cover', title = false) {
        this.sharedInternal.targetItem = {
          type: 'category',
          item: item,
          area: area,
          title: title,
          parent: this.items
        }
        let ticked = false
        this.$watch('sharedInternal.dropItem', () => {
          if (!ticked) {
            this.itemDropInternal({
              drop: this.sharedInternal.dropItem,
              target: this.sharedInternal.targetItem
            })
            this.sharedInternal.dropItem = null
            this.sharedInternal.targetItem = null
            ticked = true
            document.dispatchEvent(new CustomEvent('sp-drop-fetched-item'))
          }
        })
      },
      dropEnd (e) {
        document.dispatchEvent(new CustomEvent('sp-drop-category', {detail: e}))
      },
      itemClickInternal (item) {
        this.$emit('item-click-internal', item)
        if (this.root) {
          this.$emit('item-click', item)
        }
      },
      itemDropInternal (event) {
        this.$emit('item-drop-internal', event)
        if (this.root) {
          this.$emit('item-drop', event)
        }
      }
    }
  }
</script>

<style>
  .sp-nested-nav .sp-menu-item {
    position: relative;
  }
  .sp-nested-nav li.sp-menu-item.title {
    padding-left: 0!important;
  }
  .sp-nested-nav .el-submenu .el-menu-item.title.is-active:hover {
    border-bottom-color: #20a0ff;
  }
  .sp-nested-nav .title .sp-cover,
  .sp-nested-nav .title .sp-top,
  .sp-nested-nav .title .sp-bottom {
    left: -20px;
    width: calc(100% + 30px);
  }
</style>
