<template>
  <page-content center>
    <div style="overflow-x: auto;">
      <space class="my-1 header-actions">
        <a-button @click="onExpand">展开</a-button>
        <a-button @click="onCollapse">收起</a-button>
        <loading-action :action="onSaveOrder">保存</loading-action>
        <a-button @click="onReset">重置</a-button>
        <a-upload
          name="file"
          :custom-request="importVueRouters"
          :show-upload-list="false"
          :before-upload="confirmImport"
        >
          <a-button>导入</a-button>
        </a-upload>
        <a-button @click="onExport">导出</a-button>
      </space>

      <nested-draggable
        ref="routers"
        :expand-keys="defaultExpanded"
        class="vue-routers"
        :list="vueRouters"
        handle=".drag"
      >
        <template #default="{ data }">
          <div class="item-wrap">
            <span class="id mr-1 drag">{{ data.id }}</span>
            <svg-icon :icon-class="data.icon || ''" class="mr-1"/>
            <span>{{ data.title }}</span>
            <span class="ml-2 path">{{ data.path }}</span>
          </div>
          <div class="flex-spacer"/>
          <space style="min-width: 100px;">
            <router-link :to="`/vue-routers/create?parent_id=${data.id}`">添加</router-link>
            <router-link :to="`/vue-routers/${data.id}/edit`">编辑</router-link>
            <lz-popconfirm :confirm="onDestroy(data)" title="所有子路由都会被删除！！！">
              <a class="red-6" href="javascript:void(0);">删除</a>
            </lz-popconfirm>
          </space>
        </template>
      </nested-draggable>
    </div>
  </page-content>
</template>

<script>
import {
  destroyVueRouter,
  getVueRouters,
  updateVueRouters,
  importVueRouters,
} from '@/api/vue-routers'
import LzPopconfirm from '@c/LzPopconfirm'
import {
  getMessage,
  hasChildren,
  removeFromNested,
} from '@/libs/utils'
import NestedDraggable from '@c/NestedDraggable'
import Space from '@c/Space'
import PageContent from '@c/PageContent'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'Index',
  scroll: true,
  components: {
    Space,
    NestedDraggable,
    LzPopconfirm,
    PageContent,
    LoadingAction,
  },
  data() {
    return {
      vueRouters: [],
      vueRoutersBak: '',
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
      this.vueRoutersBak = JSON.stringify(this.vueRouters)
      this.defaultExpanded = this.vueRouters.filter(i => hasChildren(i)).map(i => i.id)

      this.$scrollResolve()
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
      const struct = []
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
      this.$message.success(getMessage('updated'))
    },
    confirmImport(file) {
      return new Promise((resolve, reject) => {
        this.$confirm({
          title: '提示',
          content: '是否替换所有路由配置？',
          onOk() {
            resolve()
          },
        })
      })
    },
    onReset() {
      this.vueRouters = JSON.parse(this.vueRoutersBak)
      this.$message.info('已重置修改，记得保存')
    },
  },
}
</script>

<style scoped lang="less">
@import "~ant-design-vue/lib/style/color/colors";

.vue-routers {
  width: 800px;

  .id {
    width: 40px;
    display: inline-block;
    text-align: center;
    font-weight: bolder;
  }

  .drag {
    cursor: move;
  }

  .path {
    color: @blue-6;
  }

  .item-wrap {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
}

.header-actions {
  width: 100%;
  overflow-x: auto;
}
</style>
