<template>
  <page-content center>
    <lz-form
      :get-data="getData"
      :submit="onSubmit"
      :form.sync="form"
      :errors.sync="errors"
    >
      <lz-form-item label="帐号" required prop="username">
        <a-input v-model="form.username"/>
      </lz-form-item>
      <lz-form-item label="姓名" required prop="name">
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="头像" prop="avatar">
        <a-input v-model="form.avatar"/>
      </lz-form-item>
      <lz-form-item label="密码" required-when-create prop="password">
        <a-input v-model="form.password" type="password"/>
      </lz-form-item>
      <lz-form-item label="确认密码" required-when-create prop="password_confirmation">
        <a-input v-model="form.password_confirmation" type="password"/>
      </lz-form-item>
      <lz-form-item label="角色" prop="roles">
        <a-select
          v-model="form.roles"
          mode="multiple"
          option-filter-prop="name"
          allow-clear
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
      <lz-form-item label="权限" prop="permissions">
        <a-select
          v-model="form.permissions"
          mode="multiple"
          option-filter-prop="name"
          allow-clear
        >
          <a-select-option
            v-for="i of permissions"
            :key="i.id"
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
  createAdminUser,
  editAdminUser,
  storeAdminUser,
  updateAdminUser,
} from '@/api/admin-users'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'

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
        username: '',
        name: '',
        password: '',
        password_confirmation: '',
        roles: [],
        permissions: [],
        avatar: '',
      },
      errors: {},
      roles: [],
      permissions: [],
    }
  },
  methods: {
    async getData($form) {
      let data

      if ($form.realEditMode) {
        ({ data } = await editAdminUser($form.resourceId))
      } else {
        ({ data } = await createAdminUser())
      }

      this.roles = data.roles
      this.permissions = data.permissions

      return data.data
    },
    async onSubmit($form) {
      if ($form.realEditMode) {
        await updateAdminUser($form.resourceId, this.form)
      } else {
        await storeAdminUser(this.form)
      }
    },
  },
}
</script>
