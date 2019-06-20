<template>
  <el-card class="create">
    <template v-slot:header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :form.sync="form"
        :errors.sync="errors"
        :edit-method="editVueRouter"
        :update-method="updateVueRouter"
        :store-method="storeVueRouter"
        redirect="/vue-routers"
      >
        <el-form-item label="父级路由" prop="parent_id">
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
          prop="title"
        >
          <el-input v-model="form.title"/>
        </el-form-item>
        <el-form-item label="地址" prop="path">
          <el-input v-model="form.path">
            <template slot="prepend">/admin/</template>
          </el-input>
        </el-form-item>
        <el-form-item label="图标" prop="icon">
          <el-input v-model="form.icon" style="width: 200px;"/>
        </el-form-item>
        <el-form-item label="排序" prop="order">
          <el-input-number v-model="form.order" :min="-9999" :max="9999"/>
        </el-form-item>
        <el-form-item label="显示在菜单" prop="menu">
          <el-switch
            v-model="form.menu"
            active-text="显示"
            inactive-text="隐藏"
          />
        </el-form-item>
        <el-form-item label="缓存" prop="cache">
          <el-switch
            v-model="form.cache"
            active-text="缓存"
            inactive-text="不缓存"
          />
        </el-form-item>
        <el-form-item label="角色" prop="roles">
          <el-select
            v-model="form.roles"
            multiple
            placeholder="选择角色"
            filterable
            clearable
          >
            <el-option
              v-for="i of roles"
              :key="i.id"
              :label="i.name"
              :value="i.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="权限" prop="permission">
          <el-select
            v-model="form.permission"
            placeholder="选择权限"
            filterable
            clearable
          >
            <el-option
              v-for="i of permissions"
              :key="i.id"
              :label="i.name"
              :value="i.slug"
            />
          </el-select>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>
<script>
import { buildVueRouterOptions } from '@/libs/utils'
import { editVueRouter, getVueRouters, storeVueRouter, updateVueRouter } from '@/api/vue-routers'
import { isInt } from '@/libs/validates'
import LzForm from '@c/LzForm'
import { getAdminRoles } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'

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
        menu: false,
        roles: [],
        permission: '',
      },
      errors: {},
      vueRouters: [],
      roles: [],
      permissions: [],
    }
  },
  computed: {
    vueRouterOptions() {
      return buildVueRouterOptions(this.vueRouters)
    },
  },
  created() {
    this.getOptions()
  },
  methods: {
    queryParentId() {
      const id = Number.parseInt(this.$route.query.parent_id)
      if (isInt(id) && this.vueRouterOptions.some(i => i.id === id)) {
        return id
      } else {
        return 0
      }
    },
    editVueRouter,
    storeVueRouter,
    updateVueRouter,
    async getOptions() {
      {
        const { data } = await getVueRouters()
        this.vueRouters = data
        !this.editMode && (this.form.parent_id = this.queryParentId())
      }
      {
        const { data } = await getAdminRoles({ all: 1 })
        this.roles = data
      }
      {
        const { data } = await getAdminPerms({ all: 1 })
        this.permissions = data
      }
    },
  },
}
</script>
