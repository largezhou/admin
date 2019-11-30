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
        disable-redirect
        disable-stay
        edit-mode
      >
        <el-form-item label="账号">
          <el-input :value="getInfo('username')" readonly/>
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
        <el-form-item label="密码" prop="password">
          <el-input
            v-model="form.password"
            type="password"
            autocomplete="new-password"
          />
        </el-form-item>
        <el-form-item label="确认密码" prop="password_confirmation">
          <el-input
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
          />
        </el-form-item>
        <el-form-item label="角色">
          <el-tag
            v-for="i of getInfo('roles', [])"
            :key="i.id"
            class="mr-1"
          >
            {{ i.name }}
          </el-tag>
        </el-form-item>
        <el-form-item label="权限">
          <el-tag
            v-for="i of getInfo('permissions', [])"
            :key="i.id"
            class="mr-1"
          >
            {{ i.name }}
          </el-tag>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>

<script>
import LzForm from '@c/LzForm'
import FormHelper from '@c/LzForm/FormHelper'
import { editUser, updateUser } from '@/api/admin-users'
import FilePicker from '@c/FilePicker'

export default {
  name: 'EditProfile',
  components: {
    LzForm,
    FilePicker,
  },
  mixins: [
    FormHelper,
  ],
  data() {
    return {
      form: {
        name: '',
        avatar: '',
        password: '',
        password_confirmation: '',
      },
      profile: null,
      errors: {},
    }
  },
  methods: {
    getInfo(key, defaultValue) {
      return this.profile ? this.profile[key] : defaultValue
    },
    async getData() {
      const { data } = await editUser()
      this.profile = data
      this.fillForm(data)
    },
    async onSubmit() {
      const { data } = await updateUser(this.form)
      this.$store.commit('SET_USER', data)
      this.form = Object.assign({}, this.form, {
        password: '',
        password_confirmation: '',
      })
    },
  },
}
</script>
