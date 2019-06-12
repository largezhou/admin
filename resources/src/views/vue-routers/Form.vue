<template>
  <el-card class="create">
    <template v-slot:header>
      <span>添加路由</span>
    </template>
    <el-row type="flex" justify="center">
      <lz-form ref="form" :model="form">
        <el-form-item label="父级路由" :error="errors.parent_id" prop="parent_id">
          <el-select
            v-model="form.parent_id"
            filterable
            clearable
            placeholder="一级"
          >
            <el-option
              v-for="i of vueRouterOptions"
              :key="i.id"
              :label="i.title"
              :value="i.id"
            >
              <span>{{ i.text }}</span>
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          label="标题"
          required
          :error="errors.title"
          prop="title"
        >
          <el-input v-model="form.title"/>
        </el-form-item>
        <el-form-item label="地址" :error="errors.path" prop="path">
          <el-input v-model="form.path">
            <template slot="prepend">/admin/</template>
          </el-input>
        </el-form-item>
        <el-form-item label="图标" :error="errors.icon" prop="icon">
          <el-input v-model="form.icon" style="width: 200px;"/>
        </el-form-item>
        <el-form-item label="排序" :error="errors.order" prop="order">
          <el-input-number v-model="form.order" :min="-9999" :max="9999"/>
        </el-form-item>
        <el-form-item label="显示在菜单" :error="errors.is_menu" prop="is_menu">
          <el-switch
            v-model="form.is_menu"
            active-text="显示"
            inactive-text="隐藏"
          />
        </el-form-item>
        <el-form-item label="缓存" :error="errors.cache" prop="cache">
          <el-switch
            v-model="form.cache"
            active-text="缓存"
            inactive-text="不缓存"
          />
        </el-form-item>
        <el-form-item>
          <loading-action type="primary" :action="onSubmit">{{ editMode ? '更新' : '添加' }}</loading-action>
          <el-button @click="onReset">重置</el-button>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>
<script>
import { assignExsits, buildVueRouterOptions, handleValidateErrors } from '@/libs/utils'
import { editVueRouter, getVueRouters, storeVueRouter, updateVueRouter } from '@/api/vue-routers'
import { isInt } from '@/libs/validates'
import LzForm from '@c/LzForm'

export default {
  name: 'Form',
  components: {
    LzForm,
  },
  data() {
    return {
      form: {
        parent_id: 0,
        title: '',
        path: '',
        icon: '',
        order: 0,
        cache: false,
        is_menu: false,
      },
      errors: {},
      vueRouters: [],
    }
  },
  computed: {
    vueRouterOptions() {
      return buildVueRouterOptions(this.vueRouters)
    },
    editMode() {
      return !!this.vueRouterId
    },
    vueRouterId() {
      return this.$route.params.id
    },
  },
  created() {
    this.getVueRouters()
    if (this.editMode) {
      this.editVueRouter()
    }
  },
  methods: {
    async onSubmit() {
      this.errors = {}
      try {
        this.editMode
          ? await this.updateVueRouter()
          : await this.storeVueRouter()
      } catch (e) {
        this.errors = handleValidateErrors(e.response)
      }
    },
    async updateVueRouter() {
      await updateVueRouter(this.vueRouterId, this.form)
      this.$router.back()
    },
    async storeVueRouter() {
      await storeVueRouter(this.form)
      this.$router.push('/vue-routers')
    },
    onReset() {
      this.$refs.form.resetFields()
    },
    async getVueRouters() {
      const { data } = await getVueRouters()
      this.vueRouters = data
      !this.editMode && (this.form.parent_id = this.queryParentId())
    },
    async editVueRouter() {
      const { data } = await editVueRouter(this.vueRouterId)
      this.form = assignExsits(this.form, data)
      await this.$nextTick()
      this.$refs.form.setInitialValues()
    },
    queryParentId() {
      const id = Number.parseInt(this.$route.query.parent_id)
      if (isInt(id) && this.vueRouterOptions.some(i => i.id === id)) {
        return id
      } else {
        return 0
      }
    },
  },
}
</script>
