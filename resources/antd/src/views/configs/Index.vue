<template>
  <page-content scroll-x>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="configs"
      bordered
      style="min-width: 1500px;"
      :pagination="false"
      table-layout="fixed"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="分类" data-index="category.name" :width="180"/>

      <a-table-column title="名称">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="name"
            :update="updateConfig"
            v-model="record.name"
          />
        </template>
      </a-table-column>
      <a-table-column title="标识">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="slug"
            :update="updateConfig"
            v-model="record.slug"
          />
        </template>
      </a-table-column>
      <a-table-column title="类型" data-index="type_text" :width="100"/>
      <a-table-column title="值" :width="450">
        <template #default="record">
          <div v-if="record.type === CONFIG_TYPES.FILE" style="display: flex; overflow-x: auto">
            <file-preview
              v-for="(item, index) of arrayWrap(record.value)"
              :key="index"
              :file="item"
            />
          </div>
          <span v-else>{{ record.value }}</span>
        </template>
      </a-table-column>
      <a-table-column title="添加时间" data-index="created_at" :width="180"/>
      <a-table-column title="修改时间" data-index="updated_at" :width="180"/>
      <a-table-column title="操作" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/configs/${record.id}/edit`">编辑</router-link>
            <lz-popconfirm :confirm="destroyConfig(record.id)">
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
import {
  getConfigCategories,
  getConfigs,
  updateConfig,
  destroyConfig,
} from '@/api/configs'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { arrayWrap, removeWhile } from '@/libs/utils'
import QuickEdit from '@c/QuickEdit'
import FilePreview from '@c/FilePreview'
import { mapConstants } from '@/libs/constants'

export default {
  name: 'Index',
  scroll: true,
  components: {
    QuickEdit,
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
    FilePreview,
  },
  data() {
    return {
      configs: [],
      page: null,

      search: [
        {
          field: 'category_id',
          label: '分类',
          type: 'select',
          options: [],
          labelField: 'name', // 默认为 name
          valueField: 'id', // 默认为 id
        },
        {
          field: 'name',
          label: '名称',
        },
        {
          field: 'slug',
          label: '标识',
        },
      ],
    }
  },
  computed: {
    ...mapConstants('CONFIG_TYPES'),
  },
  created() {
    this.getConfigCategories()
  },
  methods: {
    arrayWrap,
    destroyConfig(id) {
      return async () => {
        await destroyConfig(id)
        this.configs = removeWhile(this.configs, (i) => i.id === id)
      }
    },
    updateConfig,
    async getConfigCategories() {
      const { data } = await getConfigCategories({ all: 1 })
      this.search[0].options = data
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getConfigs(newVal.query)
        this.configs = data
        this.page = meta

        await this.$nextTick()
        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
