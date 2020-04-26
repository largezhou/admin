<template>
  <page-content center>
    <lz-form
      :get-data="getData"
      :submit="onSubmit"
      :form.sync="form"
      :errors.sync="errors"
    >
      <lz-form-item label="父级路由" prop="parent_id">
        <a-select
          v-model="form.parent_id"
          show-search
          option-filter-prop="title"
          option-label-prop="title"
        >
          <a-select-option
            v-for="i of vueRouterOptions"
            :key="i.id"
            :title="i.title"
          >
            {{ i.text }}
          </a-select-option>
        </a-select>
      </lz-form-item>
      <lz-form-item label="标题" required prop="title">
        <a-input v-model="form.title"/>
      </lz-form-item>
      <lz-form-item label="地址" prop="path" :tip="pathTip">
        <a-auto-complete :data-source="pathOptions" @search="onPathSearch"/>
      </lz-form-item>
      <lz-form-item
        class="icon-item"
        label="图标"
        prop="icon"
        tip="只有顶级菜单才会显示图标"
      >
        <a-input v-model="form.icon">
          <a-select
            v-model="form.icon"
            slot="addonBefore"
            style="width: 60px;"
          >
            <a-select-option
              v-for="i of icons"
              :key="i"
              :value="i"
            >
              <svg-icon :icon-class="i"/>
            </a-select-option>
          </a-select>
          <svg-icon
            style="width: 20px;"
            slot="addonAfter"
            :icon-class="form.icon || 'cog-fill'"
          />
        </a-input>
      </lz-form-item>
      <lz-form-item label="排序" prop="order">
        <a-input-number :min="-9999" :max="9999" v-model="form.order"/>
      </lz-form-item>
      <lz-form-item label="显示在菜单" prop="menu">
        <a-switch v-model="form.menu"/>
      </lz-form-item>
      <lz-form-item label="缓存" prop="cache">
        <a-switch v-model="form.cache"/>
      </lz-form-item>
      <lz-form-item label="角色" prop="roles">
        <a-select
          mode="multiple"
          v-model="form.roles"
          placeholder="选择角色"
          allow-clear
          show-search
          option-filter-prop="name"
        >
          <a-select-option
            v-for="i of roles"
            :key="i.id"
            :name="i.name"
          >
            {{ i.name }}
          </a-select-option>
        </a-select>
      </lz-form-item>
      <lz-form-item label="权限" prop="permission">
        <a-select
          v-model="form.permission"
          placeholder="选择权限"
          allow-clear
          show-search
          option-filter-prop="name"
        >
          <a-select-option
            v-for="i of permissions"
            :key="i.slug"
            :name="i.name"
          >
            {{ i.name }}
          </a-select-option>
        </a-select>
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import {
  createVueRouter,
  editVueRouter,
  storeVueRouter,
  updateVueRouter,
} from '@/api/vue-routers'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'
import icons from '@/icons'
import pages from '@v/pages'
import { nestedToSelectOptions, toInt } from '@/libs/utils'

const paths = Object.keys(pages).sort()

export default {
  name: 'Form',
  components: {
    PageContent,
    LzForm,
    LzFormItem,
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

      pathOptions: paths,

      pathTip: `地址可以有三种：
1：以 http 开头的完整 url，会在新窗口中打开；
2：以斜杠 '/' 开头的，不会匹配生成路由配置， 一般用于打开其他路由的不同参数的 url；
3：其他则会去匹配组件，并生成路由配置。`,
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
  methods: {
    queryParentId() {
      const id = toInt(this.$route.query.parent_id)
      if (this.vueRouterOptions.some(i => i.id === id)) {
        return id
      } else {
        return 0
      }
    },
    async getData($form) {
      let data
      if ($form.realEditMode) {
        ({ data } = await editVueRouter($form.resourceId))
      } else {
        ({ data } = await createVueRouter())
      }

      this.vueRouters = data.vue_routers
      !this.editMode && (this.form.parent_id = this.queryParentId())
      this.roles = data.roles
      this.permissions = data.permissions

      return data.data
    },
    async onSubmit($form) {
      if ($form.realEditMode) {
        await updateVueRouter($form.resourceId, this.form)
      } else {
        await storeVueRouter(this.form)
      }
    },
    onPathSearch(q) {
      q = q.trim()
      this.pathOptions = paths.filter((i) => (i.indexOf(q) !== -1))
    },
  },
}
</script>
