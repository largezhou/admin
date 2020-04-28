<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="roles"
      bordered
      :scroll="{ x: 1200 }"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="名称" data-index="name" :width="150"/>
      <a-table-column title="标识" data-index="slug" :width="150"/>
      <a-table-column title="权限">
        <template #default="record">
          <a-tag v-for="i of record.permissions" color="blue" :key="i">{{ i }}</a-tag>
        </template>
      </a-table-column>
      <a-table-column title="添加时间" data-index="created_at" :width="180"/>
      <a-table-column title="修改时间" data-index="updated_at" :width="180"/>
      <a-table-column title="操作" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/admin-roles/${record.id}/edit`">编辑</router-link>
            <lz-popconfirm :confirm="destroyAdminRole(record.id)">
              <a class="red-6" href="javascript:void(0);">删除</a>
            </lz-popconfirm>
          </space>
        </template>
      </a-table-column>
    </a-table>
    <lz-pagination :page="page"/>
  </page-content>
</template>

<script>
import { getAdminRoles, destroyAdminRole } from '@/api/admin-roles'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { removeWhile } from '@/libs/utils'

export default {
  name: 'Index',
  scroll: true,
  components: {
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
  },
  data() {
    return {
      roles: [],
      page: null,

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
    }
  },
  methods: {
    destroyAdminRole(id) {
      return async () => {
        await destroyAdminRole(id)
        this.roles = removeWhile(this.roles, (i) => i.id === id)
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminRoles(newVal.query)
        this.roles = data
        this.page = meta

        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
