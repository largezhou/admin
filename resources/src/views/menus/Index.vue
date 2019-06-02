<template>
  <el-card class="create">
    <template v-slot:header>
      <span>所有菜单</span>
    </template>
    <el-table
      :data="menus"
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
      <el-table-column prop="uri" label="地址" min-width="200"/>
      <el-table-column label="排序" width="150">
        <template v-slot="{ row }">
          <el-input-number v-model="row.order" :min="-9999" :max="9999"/>
        </template>
      </el-table-column>
      <el-table-column label="显示" width="120">
        <template v-slot="{ row }">
          <el-switch
            v-model="row.is_menu"
            active-text="是"
            inactive-text="否"
          />
        </template>
      </el-table-column>
      <el-table-column label="缓存" width="120">
        <template v-slot="{ row }">
          <el-switch
            v-model="row.cache"
            active-text="是"
            inactive-text="否"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="240">
        <template v-slot="{ row }">
          <el-button-group>
            <el-button size="small" class="link">
              <router-link :to="`/menus/create?parent_id=${row.id}`">添加子菜单</router-link>
            </el-button>
            <el-button size="small" class="link">
              <router-link :to="`/menus/${row.id}/edit`">编辑</router-link>
            </el-button>
            <pop-confirm
              type="danger"
              size="small"
              :confirm="onDestroy(row)"
              notice="所有子菜单都会被删除！！！"
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
import { destroyMenu, getMenus } from '@/api/admin-menus'
import PopConfirm from '@c/PopConfirm'
import { hasChildren } from '@/libs/utils'

export default {
  name: 'Index',
  components: {
    PopConfirm,
  },
  data() {
    return {
      menus: [],
      visible: false,
    }
  },
  created() {
    this.getMenus()
  },
  methods: {
    async getMenus() {
      const { data } = await getMenus()
      this.menus = data
    },
    onDestroy(row) {
      return async () => {
        await destroyMenu(row.id)
        this.removeMenu(this.menus, row.id)
      }
    },
    /**
     * 递归找到对应的 id 的菜单, 并从 menus 中移除
     *
     * @param menus
     * @param id
     * @returns {boolean}
     */
    removeMenu(menus, id) {
      for (let i in menus) {
        const m = menus[i]
        if (m.id === id) {
          menus.splice(i, 1)
          return true
        }

        if (hasChildren(m)) {
          if (this.removeMenu(m.children, id)) {
            return true
          }
        }
      }
      return false
    },
  },
}
</script>

<style scoped>
.el-input-number {
  width: 130px;
}
</style>
