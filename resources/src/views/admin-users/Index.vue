<template>
  <el-card>
    <template v-slot:header>
      <span>所有管理员</span>
    </template>

    <search-form :fields="search"/>

    <el-table :data="users">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="name" label="姓名" width="150"/>
      <el-table-column prop="username" label="账号" width="200"/>
      <el-table-column label="角色" min-width="400">
        <template v-slot="{ row }">
          <el-tag
            v-for="i of row.roles"
            :key="i.id"
            size="small"
            class="tag"
          >
            {{ i.name }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column label="权限" min-width="400">
        <template v-slot="{ row }">
          <el-tag
            v-for="i of row.permissions"
            :key="i.id"
            size="small"
            class="tag"
          >
            {{ i.name }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="添加时间" width="180"/>
      <el-table-column prop="updated_at" label="修改时间" width="180"/>
      <el-table-column label="操作" width="150">
        <template v-slot="{ row, $index }">
          <el-button-group>
            <el-button size="small" class="link">
              <router-link :to="`/admin-users/${row.id}/edit`">编辑</router-link>
            </el-button>
            <pop-confirm
              type="danger"
              size="small"
              :confirm="onDestroy($index)"
            >
              删除
            </pop-confirm>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <div class="card-footer">
      <pagination :page="page"/>
    </div>
  </el-card>
</template>

<script>
import SearchForm from '@c/SearchForm'
import Pagination from '@c/Pagination'
import { destroyAdminUser, getAdminUsers } from '@/api/admin-users'
import { getMessage } from '@/libs/utils'
import PopConfirm from '@c/PopConfirm'

export default {
  name: 'Index',
  components: {
    SearchForm,
    Pagination,
    PopConfirm,
  },
  data() {
    return {
      search: [
        {
          field: 'id',
          label: 'ID',
        },
        {
          field: 'name',
          label: '姓名',
        },
        {
          field: 'username',
          label: '账号',
        },
        {
          field: 'role_name',
          label: '角色',
        },
        {
          field: 'permission_name',
          label: '权限',
        },
      ],
      users: [],
      page: null,
    }
  },
  methods: {
    onDestroy(index) {
      return async () => {
        await destroyAdminUser(this.users[index].id)
        this.users.splice(index, 1)
        this.$message.success(getMessage('destroyed'))
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminUsers(newVal.query)
        this.users = data
        this.page = meta
      },
      immediate: true,
    },
  },
}
</script>

<style scoped>

</style>
