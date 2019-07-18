<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>

    <el-button-group class="mb-3">
      <el-button @click="searchShow = !searchShow">筛选</el-button>
      <el-button @click="createDialog = true">添加</el-button>
    </el-button-group>

    <search-form :show="searchShow" :fields="search"/>

    <el-table :data="cates">
      <el-table-column prop="id" label="ID" width="60"/>
      <el-table-column prop="name" label="名称" min-width="180">
        <template #default="{ row, $index }">
          <input-edit
            :id="row.id"
            field="name"
            :update="updateConfigCategory"
            v-model="row.name"
          />
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="添加时间" width="180"/>
      <el-table-column prop="updated_at" label="修改时间" width="180"/>
      <el-table-column label="操作" width="160">
        <template v-slot="{ row, $index }">
          <el-button-group>
            <button-link size="small" :to="'/'">查看配置</button-link>
            <pop-confirm
              type="danger"
              :confirm="onDestroy($index)"
              notice="分类下的所有配置都会被删除"
              size="small"
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

    <el-dialog
      title="添加分类"
      :visible.sync="createDialog"
      width="400px"
      @keydown.enter.native="$refs.saveConfirm.onAction"
      append-to-body
    >
      <el-input
        autofocus
        autocomplete="off"
        v-model="name"
      />
      <div slot="footer" class="dialog-footer">
        <el-button @click="createDialog = false">取消</el-button>
        <loading-action
          ref="saveConfirm"
          :disabled="!name"
          type="primary"
          :action="onStoreCategory"
        >
          确定
        </loading-action>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
import SearchForm from '@c/SearchForm'
import Pagination from '@c/Pagination'
import {
  destroyConfigCategory,
  getConfigCategories,
  storeConfigCategory,
  updateConfigCategory,
} from '@/api/admin-configs'
import ButtonLink from '@c/ButtonLink'
import PopConfirm from '@c/PopConfirm'
import { getMessage } from '@/libs/utils'
import InputEdit from '@c/quick-edit/InputEdit'
import InputNumberEdit from '@c/quick-edit/InputNumberEdit'

export default {
  name: 'Index',
  components: {
    InputNumberEdit,
    InputEdit,
    PopConfirm,
    ButtonLink,
    SearchForm,
    Pagination,
  },
  data() {
    return {
      searchShow: false,
      search: [
        {
          field: 'name',
          label: '名称',
        },
      ],

      cates: [],
      page: null,

      createDialog: false,
      name: '',
    }
  },
  methods: {
    async onStoreCategory() {
      const { data } = await storeConfigCategory({ name: this.name })
        .config({ showValidationMsg: true })

      this.createDialog = false
      this.$message.success(getMessage('created'))
      this.cates.unshift(data)
    },
    onDestroy(index) {
      return async () => {
        await destroyConfigCategory(this.cates[index].id)
        this.cates.splice(index, 1)
        this.$message.success(getMessage('destroyed'))
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
      },
      immediate: true,
    },
    createDialog(newVal) {
      if (!newVal) {
        this.name = ''
      }
    },
  },
}
</script>
