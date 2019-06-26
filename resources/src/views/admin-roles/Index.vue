<template>
  <el-card>
    <template v-slot:header>
      <content-header/>
    </template>

    <search-form :fields="search"/>

    <el-table :data="roles">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="name" label="名称" width="150"/>
      <el-table-column prop="slug" label="标识" width="150"/>
      <el-table-column label="权限" min-width="400">
        <template v-slot="{ row }">
          <el-tag
            v-for="i of row.permissions"
            :key="i.id"
            class="mr-1"
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
              <router-link :to="`/admin-roles/${row.id}/edit`">编辑</router-link>
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
import { destroyAdminRole, getAdminRoles } from '@/api/admin-roles'
import { getMessage } from '@/libs/utils'
import Pagination from '@c/Pagination'
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
          label: '名称',
        },
        {
          field: 'slug',
          label: '标识',
        },
        {
          field: 'permission_name',
          label: '权限',
        },
      ],
      roles: [],
      page: null,
    }
  },
  methods: {
    onDestroy(index) {
      return async () => {
        await destroyAdminRole(this.roles[index].id)
        this.roles.splice(index, 1)
        this.$message.success(getMessage('destroyed'))
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminRoles(newVal.query)
        this.roles = data
        this.page = meta
      },
      immediate: true,
    },
  },
}
</script>
