<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>

    <el-button-group class="mb-3">
      <search-form :fields="search"/>
    </el-button-group>

    <el-table :data="configs" resource="configs">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="category.name" label="分类" width="180"/>
      <el-table-column prop="name" label="名称" width="180">
        <template #default="{ row }">
          <input-edit
            :id="row.id"
            field="name"
            :update="updateConfig"
            v-model="row.name"
          />
        </template>
      </el-table-column>
      <el-table-column prop="slug" label="标识" width="150">
        <template #default="{ row }">
          <input-edit
            :id="row.id"
            field="slug"
            :update="updateConfig"
            v-model="row.slug"
          />
        </template>
      </el-table-column>
      <el-table-column prop="type_text" label="类型" width="100"/>
      <el-table-column prop="value" label="值" min-width="300">
        <template #default="{ row }">
          <div v-if="row.type === CONFIG_TYPES.FILE" style="display: flex; overflow-x: auto">
            <template v-if="Array.isArray(row.value)">
              <file-preview
                v-for="(item, index) of row.value"
                :key="index"
                :file="item"
              />
            </template>
            <file-preview v-else-if="row.value" :file="row.value"/>
          </div>
          <json-show v-else :json="row.value"/>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="添加时间" width="160"/>
      <el-table-column prop="updated_at" label="修改时间" width="160"/>
      <el-table-column label="操作" width="140">
        <template #default="scoped">
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
import {
  getConfigCategories,
  getConfigs,
  updateConfig,
} from '@/api/configs'
import Pagination from '@c/Pagination'
import InputEdit from '@c/quick-edit/InputEdit'
import RowDestroy from '@c/LzTable/RowDestroy'
import RowToEdit from '@c/LzTable/RowToEdit'
import JsonShow from '@c/JsonShow'
import { mapConstants } from '@/libs/constants'
import FilePreview from '@c/FilePreview'

export default {
  name: 'Index',
  components: {
    JsonShow,
    RowToEdit,
    SearchForm,
    Pagination,
    InputEdit,
    RowDestroy,
    FilePreview,
  },
  data() {
    return {
      search: [
        {
          field: 'category_id',
          label: '分类',
          type: 'el-select',
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

      configs: [],
      page: null,
    }
  },
  computed: {
    ...mapConstants('CONFIG_TYPES'),
  },
  created() {
    this.getConfigCategories()
  },
  methods: {
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
      },
      immediate: true,
    },
  },
}
</script>
