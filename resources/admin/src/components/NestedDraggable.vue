<template>
  <draggable
    class="item-wrapper"
    tag="div"
    :list="list"
    :handle="handle"
    :group="group"
  >
    <div class="item-group" :key="el[dataKey]" v-for="el in list">
      <div class="item">
        <div
          v-show="hasChildren(el)"
          @click="onToggleExpand(el[dataKey])"
          class="expand-icon"
          :class="{ expanded: isExpanded(el[dataKey]) }"
        >
          <i class=" el-icon-arrow-right"/>
        </div>
        <slot :data="el"/>
      </div>
      <!--没有下级时，不能隐藏，不然其他节点，无法拖入到该节点的下级，除非手动展开-->
      <nested-draggable
        v-show="!hasChildren(el) || isExpanded(el[dataKey])"
        v-bind="passProps"
        class="item-sub"
        :list="el.children"
      >
        <template #default="{ data }">
          <slot :data="data"/>
        </template>
      </nested-draggable>
    </div>
  </draggable>
</template>

<script>
import Draggable from 'vuedraggable'
import { hasChildren } from '@/libs/utils'
import _omit from 'lodash/omit'
import Emitter from 'element-ui/lib/mixins/emitter'

const EXPAND_ALL_EVENT_KEY = 'nested-draggable.expand-all'
const COLLAPSE_ALL_EVENT_KEY = 'nested-draggable.collapse-all'

export default {
  name: 'NestedDraggable',
  componentName: 'NestedDraggable',
  components: {
    Draggable,
  },
  mixins: [
    Emitter,
  ],
  data() {
    return {
      expanded: [],
      collapseComponent: 'el-collapse-transition',
    }
  },
  props: {
    list: Array,
    group: {
      type: String,
      default: 'default',
    },
    handle: {
      type: String,
      default: '.item',
    },
    expandKeys: {
      type: Array,
      default: () => [],
    },
    dataKey: {
      type: String,
      default: 'id',
    },
  },
  computed: {
    passProps() {
      return _omit(this.$props, [
        'list',
      ])
    },
    listKeys() {
      return this.list.map((i) => i[this.dataKey])
    },
  },
  created() {
    this.$on(EXPAND_ALL_EVENT_KEY, this.expandAll)
    this.$on(COLLAPSE_ALL_EVENT_KEY, this.collapseAll)
  },
  beforeDestroy() {
    this.$off(EXPAND_ALL_EVENT_KEY, this.expandAll)
    this.$off(COLLAPSE_ALL_EVENT_KEY, this.collapseAll)
  },
  methods: {
    hasChildren,
    onToggleExpand(id) {
      const index = this.expanded.indexOf(id)
      if (index === -1) {
        this.expanded.push(id)
      } else {
        this.expanded.splice(index, 1)
      }
    },
    isExpanded(id) {
      return this.expanded.indexOf(id) !== -1
    },
    expandAll() {
      this.expanded = [...this.listKeys]
      this.broadcast('NestedDraggable', EXPAND_ALL_EVENT_KEY)
    },
    collapseAll() {
      this.expanded = []
      this.broadcast('NestedDraggable', COLLAPSE_ALL_EVENT_KEY)
    },
  },
  watch: {
    expandKeys: {
      handler(newVal) {
        this.expanded = newVal
      },
      immediate: true,
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.item-wrapper {
  max-width: 100%;
  margin: 0;
}

.item {
  height: 50px;
  display: flex;
  padding: 0 15px 0 5px;
  align-items: center;
  border: $--table-border;
  transition: background-color .25s ease;

  &:hover {
    background-color: $--background-color-base;
  }
}

.item-sub {
  margin-left: 20px;
}

.sortable-ghost {
  background-color: $--color-primary-light-8;
}

.expand-icon {
  cursor: pointer;
  color: $--icon-color;
  padding: 5px;
}

.expanded {
  transform: rotate(90deg);
}
</style>
