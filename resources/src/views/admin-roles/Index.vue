<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>

    <el-button-group class="mb-3">
      <search-form :fields="search"/>
    </el-button-group>

    <el-table :data="roles" resource="admin-roles">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="name" label="名称" width="150"/>
      <el-table-column prop="slug" label="标识" width="150"/>
      <el-table-column label="权限" min-width="400">
        <template #default="{ row }">
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
        <template #default="{ row, $index }">
          <el-button-group>
            <row-to-edit/>
            <row-destroy/>
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
import { getAdminRoles } from '@/api/admin-roles'
import Pagination from '@c/Pagination'
import RowDestroy from '@c/LzTable/RowDestroy'
import RowToEdit from '@c/LzTable/RowToEdit'

export default {
  name: 'Index',
  components: {
    RowToEdit,
    RowDestroy,
    SearchForm,
    Pagination,
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
