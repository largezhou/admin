<template>
  <page-content scroll-x>
    <space class="my-1">
      <search-form :fields="search"/>
      <a-button @click="createDialog = true">添加</a-button>
    </space>

    <a-table
      row-key="id"
      :data-source="cates"
      bordered
      style="min-width: 950px;"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="名称" :width="150">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="name"
            :update="updateConfigCategory"
            v-model="record.name"
          />
        </template>
      </a-table-column>
      <a-table-column title="标识">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="slug"
            :update="updateConfigCategory"
            v-model="record.slug"
          />
        </template>
      </a-table-column>
      <a-table-column title="添加时间" data-index="created_at" :width="180"/>
      <a-table-column title="修改时间" data-index="updated_at" :width="180"/>
      <a-table-column title="操作" :width="200">
        <template #default="record">
          <space>
            <router-link :to="`/configs/create?category_id=${record.id}`">添加配置</router-link>
            <router-link :to="`/configs?category_id=${record.id}`">查看配置</router-link>
            <lz-popconfirm :confirm="destroyConfigCategory(record.id)">
              <a class="red-6" href="javascript:void(0);">删除</a>
            </lz-popconfirm>
          </space>
        </template>
      </a-table-column>
    </a-table>
    <lz-pagination :page="page"/>

    <a-modal
      title="添加分类"
      v-model="createDialog"
      :footer="null"
      width="400px"
    >
      <lz-form
        ref="form"
        disable-redirect
        :form.sync="form"
        :errors.sync="errors"
        :submit="onStoreCategory"
        in-dialog
        layout="vertical"
      >
        <lz-form-item label="名称" required prop="name">
          <a-input v-model="form.name" focus/>
        </lz-form-item>
        <lz-form-item label="标识" required prop="slug">
          <a-input v-model="form.slug"/>
        </lz-form-item>
      </lz-form>
    </a-modal>
  </page-content>
</template>

<script>
import {
  getConfigCategories,
  storeConfigCategory,
  updateConfigCategory,
  destroyConfigCategory,
} from '@/api/configs'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { removeWhile } from '@/libs/utils'
import LzForm from '@c/LzForm/index'
import LzFormItem from '@c/LzForm/LzFormItem'
import QuickEdit from '@c/QuickEdit'

export default {
  name: 'Index',
  scroll: true,
  components: {
    QuickEdit,
    LzFormItem,
    LzForm,
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
  },
  data() {
    return {
      cates: [],
      page: null,

      search: [
        {
          field: 'name',
          label: '名称',
        },
        {
          field: 'slug',
          label: '标识',
        },
      ],

      createDialog: false,

      form: {
        name: '',
        slug: '',
      },
      errors: {},
    }
  },
  methods: {
    destroyConfigCategory(id) {
      return async () => {
        await destroyConfigCategory(id)
        this.cates = removeWhile(this.cates, (i) => i.id === id)
      }
    },
    async onStoreCategory($form) {
      const { data } = await storeConfigCategory(this.form)

      this.cates.unshift(data)

      if ($form.stay) {
        $form.onReset()
      } else {
        this.createDialog = false
      }
    },
    updateConfigCategory,
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getConfigCategories(newVal.query)
        this.cates = data
        this.page = meta

        await this.$nextTick()
        this.$scrollResolve()
      },
      immediate: true,
    },
    createDialog(newVal) {
      if (!newVal) {
        this.$refs.form.onReset()
      }
    },
  },
}
</script>
