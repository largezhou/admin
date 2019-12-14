<template>
  <el-card class="vue-routers-index">
    <template #header>
      <content-header/>
    </template>

    <el-row type="flex" justify="center">
      <div style="overflow-x: auto;">
        <el-button-group class="mb-3">
          <el-button @click="onExpand">展开</el-button>
          <el-button @click="onCollapse">收起</el-button>
          <loading-action :action="onSaveOrder">保存</loading-action>
          <el-button class="ml-1 pa-0">
            <el-upload
              action=""
              :show-file-list="false"
              :http-request="importVueRouters"
              :before-upload="confirmImport"
            >
              <el-button style="border: none;">导入</el-button>
            </el-upload>
          </el-button>
          <el-button @click="onExport">导出</el-button>
        </el-button-group>
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
            <collapse-button-group min-width="166px">
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
    </el-row>
  </el-card>
</template>

<script>
import {
  destroyVueRouter,
  getVueRouters,
  updateVueRouters,
  importVueRouters,
} from '@/api/vue-routers'
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
    onExport() {
      const blob = new Blob(
        [JSON.stringify(this.vueRouters, null, 2)],
        { type: 'application/json' },
      )

      const link = document.createElement('a')
      link.href = window.URL.createObjectURL(blob)
      link.download = '路由配置'
      link.click()

      window.URL.revokeObjectURL(link.href)
    },
    async importVueRouters({ file }) {
      const fd = new FormData()
      fd.append('file', file)

      const { data } = await importVueRouters(fd)
      this.vueRouters = data
    },
    confirmImport() {
      return this.$confirm('是否替换所有路由配置？', '提示', {
        confirmButtonText: '替换',
        type: 'warning',
      })
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.vue-routers {
  width: 800px;

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
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
}
</style>
