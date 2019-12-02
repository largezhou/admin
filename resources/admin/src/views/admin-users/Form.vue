<template>
  <el-card>
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
        <el-form-item label="账号" required prop="username">
          <el-input v-model="form.username"/>
        </el-form-item>
        <el-form-item label="姓名" required prop="name">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="头像" prop="avatar">
          <file-picker
            v-model="form.avatar"
            ext="jpg,gif,png,jpeg"
          />
        </el-form-item>
        <el-form-item label="密码" :required="!editMode" prop="password">
          <el-input
            v-model="form.password"
            type="password"
            autocomplete="new-password"
          />
        </el-form-item>
        <el-form-item label="确认密码" :required="!editMode" prop="password_confirmation">
          <el-input
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
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
        <el-form-item label="权限" prop="permissions">
          <el-select
            v-model="form.permissions"
            multiple
            placeholder="选择权限"
            filterable
            clearable
          >
            <el-option
              v-for="i of permissions"
              :key="i.id"
              :label="i.name"
              :value="i.id"
            />
          </el-select>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>

<script>
import LzForm from '@c/LzForm'
import { createAdminUser, editAdminUser, storeAdminUser, updateAdminUser } from '@/api/admin-users'
import { getAdminRoles } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'
import FormHelper from '@c/LzForm/FormHelper'
import FilePicker from '@c/FilePicker'

export default {
  name: 'Form',
  components: {
    FilePicker,
    LzForm,
  },
  mixins: [
    FormHelper,
  ],
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
    async onSubmit() {
      if (this.editMode) {
        await updateAdminUser(this.resourceId, this.form)
      } else {
        await storeAdminUser(this.form)
      }
    },
    async getData() {
      let data

      if (this.editMode) {
        ({ data } = await editAdminUser(this.resourceId))
        this.fillForm(data.admin_user)
      } else {
        ({ data } = await createAdminUser())
      }

      this.roles = data.roles
      this.permissions = data.permissions
    },
  },
}
</script>
