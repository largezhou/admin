<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="users"
      bordered
      :scroll="{ x: 1500 }"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="姓名" data-index="name" :width="150"/>
      <a-table-column title="帐号" data-index="username" :width="200"/>
      <a-table-column title="角色">
        <template #default="record">
          <a-tag v-for="i of record.roles" color="blue" :key="i">{{ i }}</a-tag>
        </template>
      </a-table-column>
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
            <router-link :to="`/admin-users/${record.id}/edit`">编辑</router-link>
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
import { getAdminUsers, destroyAdminUser } from '@/api/admin-users'
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
      users: [],
      page: null,

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
    }
  },
  methods: {
    destroyAdminRole(id) {
      return async () => {
        await destroyAdminUser(id)
        this.users = removeWhile(this.users, (i) => i.id === id)
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminUsers(newVal.query)
        this.users = data
        this.page = meta

        await this.$nextTick()
        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
