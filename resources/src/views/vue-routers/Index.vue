<template>
  <el-card class="create">
    <template v-slot:header>
      <span>所有路由</span>
    </template>
    <el-table
      :data="vueRouters"
      style="width: 100%;margin-bottom: 20px;"
      row-key="id"
      border
      :default-expand-all="false"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column width="200">
        <template v-slot:header="scope">标题</template>
        <template v-slot="{ row }">
          <i :class="row.icon"/>
          <span>{{ row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="path" label="地址" min-width="200"/>
      <el-table-column label="排序" width="150">
        <template v-slot="{ row }">
          <input-number-edit
            :id="row.id"
            field="order"
            :update="updateVueRouter"
            v-model="row.order"
            :min="-9999"
            :max="9999"
          />
        </template>
      </el-table-column>
      <el-table-column label="显示在菜单" width="120">
        <template v-slot="{ row }">
          <switch-edit
            :id="row.id"
            field="is_menu"
            v-model="row.is_menu"
            active-text="是"
            inactive-text="否"
            :update="updateVueRouter"
          />
        </template>
      </el-table-column>
      <el-table-column label="缓存" width="120">
        <template v-slot="{ row }">
          <switch-edit
            :id="row.id"
            field="cache"
            v-model="row.cache"
            active-text="是"
            inactive-text="否"
            :update="updateVueRouter"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="240">
        <template v-slot="{ row }">
          <el-button-group>
            <el-button size="small" class="link">
              <router-link :to="`/vue-routers/create?parent_id=${row.id}`">添加子路由</router-link>
            </el-button>
            <el-button size="small" class="link">
              <router-link :to="`/vue-routers/${row.id}/edit`">编辑</router-link>
            </el-button>
            <pop-confirm
              type="danger"
              size="small"
              :confirm="onDestroy(row)"
              notice="所有子路由都会被删除！！！"
            >
              删除
            </pop-confirm>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script>
import { destroyVueRouter, getVueRouters, updateVueRouter } from '@/api/vue-routers'
import PopConfirm from '@c/PopConfirm'
import { hasChildren } from '@/libs/utils'
import SwitchEdit from '@c/quick-edit/SwitchEdit'
import InputNumberEdit from '@c/quick-edit/InputNumberEdit'

export default {
  name: 'Index',
  components: {
    InputNumberEdit,
    SwitchEdit,
    PopConfirm,
  },
  data() {
    return {
      vueRouters: [],
      visible: false,
    }
  },
  created() {
    this.getVueRouters()
  },
  methods: {
    async getVueRouters() {
      const { data } = await getVueRouters()
      this.vueRouters = data
    },
    onDestroy(row) {
      return async () => {
        await destroyVueRouter(row.id)
        this.removeVueRouter(this.vueRouters, row.id)
      }
    },
    /**
     * 递归找到对应的 id 的路由, 并从 vueRouters 中移除
     *
     * @param vueRouters
     * @param id
     * @returns {boolean}
     */
    removeVueRouter(vueRouters, id) {
      for (let i in vueRouters) {
        const m = vueRouters[i]
        if (m.id === id) {
          vueRouters.splice(i, 1)
          return true
        }

        if (hasChildren(m)) {
          if (this.removeVueRouter(m.children, id)) {
            return true
          }
        }
      }
      return false
    },
    updateVueRouter,
  },
}
</script>

<style scoped>
.el-input-number {
  width: 130px;
}
</style>
