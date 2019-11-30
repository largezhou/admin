<template>
  <el-card class="create">
    <template #header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :get-data="getData"
        :submit="onSubmit"
        :errors.sync="errors"
        :form.sync="form"
        :edit-mode="editMode"
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
        <el-form-item label="地址" prop="path" :helper="pathHelper">
          <el-autocomplete
            class="w-100"
            v-model="form.path"
            :fetch-suggestions="pathSearch"
            clearable
          />
        </el-form-item>
        <el-form-item label="图标" prop="icon">
          <el-input v-model="form.icon" class="icon">
            <el-select v-model="form.icon" slot="prepend" placeholder="图标">
              <el-option
                v-for="i of icons"
                :key="i"
                label="图标"
                :value="i"
              >
                <svg-icon class="mr-2" :icon-class="i"/>
                <span class="fr">{{ i }}</span>
              </el-option>
            </el-select>
            <svg-icon slot="append" :icon-class="form.icon || 'cog-fill'"/>
          </el-input>
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
import { nestedToSelectOptions, toInt } from '@/libs/utils'
import { createVueRouter, editVueRouter, getVueRouters, storeVueRouter, updateVueRouter } from '@/api/vue-routers'
import LzForm from '@c/LzForm'
import { getAdminRoles } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'
import FormHelper from '@c/LzForm/FormHelper'
import icons from '@/icons'
import pages from '@v/pages'

export default {
  name: 'Form',
  components: {
    LzForm,
  },
  mixins: [
    FormHelper,
  ],
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

      pathHelper: '地址可以有三种：\n' +
        '1：以 http 开头的完整 url，会在新窗口中打开；\n' +
        '2：以斜杠 \'/\' 开头的，不会匹配生成路由配置，\n' +
        '一般用于打开其他路由的不同参数的 url；\n' +
        '3：其他则会去匹配组件，并生成路由配置。',
    }
  },
  computed: {
    vueRouterOptions() {
      return nestedToSelectOptions(this.vueRouters)
    },
    icons() {
      return icons
    },
  },
  created() {
    this.initPaths()
  },
  methods: {
    queryParentId() {
      const id = toInt(this.$route.query.parent_id)
      if (this.vueRouterOptions.some(i => i.id === id)) {
        return id
      } else {
        return 0
      }
    },
    async onSubmit() {
      if (this.editMode) {
        await updateVueRouter(this.resourceId, this.form)
      } else {
        await storeVueRouter(this.form)
      }
    },
    async getData() {
      let data
      if (this.editMode) {
        ({ data } = await editVueRouter(this.resourceId))
        this.fillForm(data.vue_router)
      } else {
        ({ data } = await createVueRouter())
      }

      this.vueRouters = data.vue_routers
      !this.editMode && (this.form.parent_id = this.queryParentId())
      this.roles = data.roles
      this.permissions = data.permissions
    },
    initPaths() {
      this.paths = Object.keys(pages).sort().map((i) => ({ value: i }))
    },
    pathSearch(q, cb) {
      const results = q
        ? this.paths.filter((i) => (i.value.indexOf(q) !== -1))
        : this.paths
      cb(results)
    },
  },
}
</script>

<style lang="scss">
.icon {
  .el-select .el-input {
    width: 80px;
  }
}
</style>
