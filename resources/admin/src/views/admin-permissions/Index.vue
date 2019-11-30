<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>

    <el-button-group class="mb-3">
      <search-form :fields="search"/>
    </el-button-group>

    <el-table :data="perms" resource="admin-permissions">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="name" label="名称" width="150"/>
      <el-table-column prop="slug" label="标识" width="150"/>
      <el-table-column label="路由" min-width="400">
        <template #default="{ row }">
          <route-show :data="row"/>
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
import { getAdminPerms } from '@/api/admin-perms'
import RouteShow from './components/RouteShow'
import Pagination from '@c/Pagination'
import SearchForm from '@c/SearchForm'
import RowDestroy from '@c/LzTable/RowDestroy'
import RowToEdit from '@c/LzTable/RowToEdit'

export default {
  name: 'Index',
  components: {
    RowToEdit,
    RowDestroy,
    SearchForm,
    Pagination,
    RouteShow,
  },
  data() {
    return {
      perms: [],
      page: null,
      searchForm: {},

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
          field: 'http_path',
          label: '请求路径',
        },
      ],
    }
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminPerms(newVal.query)
        this.perms = data
        this.page = meta
      },
      immediate: true,
    },
  },
}
</script>
