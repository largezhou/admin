<template>
  <el-card class="vue-routers-index">
    <template #header>
      <content-header/>
    </template>

    <el-button-group class="mb-3">
      <el-button @click="onExpand">展开</el-button>
      <el-button @click="onCollapse">收起</el-button>
      <loading-action :action="onSaveOrder">保存</loading-action>
    </el-button-group>

    <div style="overflow-x: auto">
      <nested-draggable
        ref="routers"
        :expand-keys="defaultExpanded"
        class="vue-routers"
        :list="vueRouters"
        handle=".drag-zone"
      >
        <template #default="{ data }">
          <div class="drag-zone">
            <span class="id mr-1">{{ data.id }}</span>
            <svg-icon :icon-class="data.icon || ''" class="mr-1"/>
            <span>{{ data.title }}</span>
            <span class="ml-2 path">{{ data.path }}</span>
          </div>
          <flex-spacer/>
          <collapse-button-group>
            <button-link size="small" :to="`/vue-routers/create?parent_id=${data.id}`">添加</button-link>
            <button-link size="small" :to="`/vue-routers/${data.id}/edit`">编辑</button-link>
            <pop-confirm
              type="danger"
              size="small"
              :confirm="onDestroy(data)"
              notice="所有子路由都会被删除！！！"
            >
              删除
            </pop-confirm>
          </collapse-button-group>
        </template>
      </nested-draggable>
    </div>
  </el-card>
</template>

<script>
import { destroyVueRouter, getVueRouters, updateVueRouters } from '@/api/vue-routers'
import PopConfirm from '@c/PopConfirm'
import { getMessage, hasChildren, removeFromNested } from '@/libs/utils'
import NestedDraggable from '@c/NestedDraggable'
import FlexSpacer from '@c/FlexSpacer'
import CollapseButtonGroup from '@c/CollapseButtonGroup'
import ButtonLink from '@c/ButtonLink'

export default {
  name: 'Index',
  components: {
    ButtonLink,
    CollapseButtonGroup,
    FlexSpacer,
    NestedDraggable,
    PopConfirm,
  },
  data() {
    return {
      vueRouters: [],
      visible: false,
      // 默认展开第二级路由
      defaultExpanded: [],
    }
  },
  created() {
    this.getVueRouters()
  },
  methods: {
    async getVueRouters() {
      const { data } = await getVueRouters()
      this.vueRouters = data
      this.defaultExpanded = this.vueRouters.filter(i => hasChildren(i)).map(i => i.id)
    },
    onDestroy(row) {
      return async () => {
        await destroyVueRouter(row.id)
        this.$message.success(getMessage('destroyed'))
        removeFromNested(this.vueRouters, row.id)
      }
    },
    hasChildren,
    onExpand() {
      this.$refs.routers.expandAll()
    },
    onCollapse() {
      this.$refs.routers.collapseAll()
    },
    /**
     * 获取路由结构，仅包含 id 和 children 字段
     * @param vueRouters
     * @returns {Array}
     */
    getVueRouterStruct(vueRouters = this.vueRouters) {
      let struct = []
      vueRouters.forEach((i) => {
        struct.push({
          id: i.id,
          children: hasChildren(i)
            ? this.getVueRouterStruct(i.children)
            : undefined,
        })
      })
      return struct
    },
    async onSaveOrder() {
      await updateVueRouters({
        _order: this.getVueRouterStruct(this.vueRouters),
      })
      this.$message.success(getMessage('updated'))
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.vue-routers {
  min-width: 600px;

  .id {
    width: 40px;
    display: inline-block;
    text-align: center;
    font-weight: bolder;
  }

  .path {
    color: $--color-primary;
  }

  .drag-zone {
    cursor: move;
  }
}
</style>
