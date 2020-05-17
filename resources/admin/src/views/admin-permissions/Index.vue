<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="perms"
      bordered
      :scroll="{ x: 1200 }"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="名称" data-index="name" :width="150"/>
      <a-table-column title="标识" data-index="slug" :width="150"/>
      <a-table-column title="路由">
        <template #default="record">
          <route-show :data="record"/>
        </template>
      </a-table-column>
      <a-table-column title="添加时间" data-index="created_at" :width="180"/>
      <a-table-column title="修改时间" data-index="updated_at" :width="180"/>
      <a-table-column title="操作" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/admin-permissions/${record.id}/edit`">编辑</router-link>
            <lz-popconfirm :confirm="destroyAdminPerm(record.id)">
              <a class="error-color" href="javascript:void(0);">删除</a>
            </lz-popconfirm>
          </space>
        </template>
      </a-table-column>
    </a-table>
    <lz-pagination :page="page"/>
  </page-content>
</template>

<script>
import { getAdminPerms, destroyAdminPerm } from '@/api/admin-perms'
import RouteShow from './components/RouteShow'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { removeWhile } from '@/libs/utils'

export default {
  name: 'Index',
  /**
   * 延迟路由的滚动行为，配合 this.$$scrollResolve()
   * 可在数据加载完后，再滚动
   */
  scroll: true,
  components: {
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
    RouteShow,
  },
  data() {
    return {
      perms: [],
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
          field: 'http_path',
          label: '请求路径',
        },
      ],
    }
  },
  methods: {
    destroyAdminPerm(id) {
      return async () => {
        await destroyAdminPerm(id)
        this.perms = removeWhile(this.perms, (i) => i.id === id)
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminPerms(newVal.query)
        this.perms = data
        this.page = meta

        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
