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
      <el-table-column label="操作" width="180">
        <template v-slot="{ row, index }">
          <el-button-group>
            <el-button type="primary" size="small" class="link">
              <router-link :to="editLink(row.id)">编辑</router-link>
            </el-button>
            <el-button type="danger" size="small">删除</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script>
import { getMenus } from '@/api/admin-menus'

export default {
  name: 'Index',
  data() {
    return {
      menus: [],
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
    test(scope) {
      log(scope)
    },
    editLink(id) {
      return `/menus/${id}/edit`
    },
  },
}
</script>

<style scoped>
.el-input-number {
  width: 130px;
}
</style>
