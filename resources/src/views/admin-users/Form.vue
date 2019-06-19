<template>
  <el-card>
    <template v-slot:header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :edit-method="editAdminUser"
        :store-method="storeAdminUser"
        :update-method="updateAdminUser"
        redirect="/admin-users"
        :form.sync="form"
        :errors.sync="errors"
      >
        <el-form-item label="账号" required prop="username">
          <el-input v-model="form.username"/>
        </el-form-item>
        <el-form-item label="姓名" required prop="name">
          <el-input v-model="form.name"/>
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
import { editAdminUser, storeAdminUser, updateAdminUser } from '@/api/admin-users'
import { getAdminRoles } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'
import EditHelper from '@c/LzForm/EditHelper'

export default {
  name: 'Form',
  components: {
    LzForm,
  },
  mixins: [
    EditHelper,
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
      },
      errors: {},
      roles: [],
      permissions: [],
    }
  },
  created() {
    this.getOptions()
  },
  methods: {
    editAdminUser,
    storeAdminUser,
    updateAdminUser,
    async getOptions() {
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
