<template>
  <page-content center>
    <lz-form
      ref="form"
      :get-data="getData"
      :submit="onSubmit"
      :errors.sync="errors"
      :form.sync="form"
      disable-stay
      disable-redirect
      edit-mode
    >
      <lz-form-item label="帐号">
        <a-input :value="profile.username" read-only/>
      </lz-form-item>
      <lz-form-item label="姓名" required prop="name">
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="密码" prop="password">
        <a-input type="password" v-model="form.password"/>
      </lz-form-item>
      <lz-form-item label="确认密码" prop="password_confirmation">
        <a-input type="password" v-model="form.password_confirmation"/>
      </lz-form-item>
      <lz-form-item label="角色">
        <a-tag v-for="i of profile.roles" color="blue" :key="i">{{ i }}</a-tag>
      </lz-form-item>
      <lz-form-item label="权限">
        <a-tag v-for="i of profile.permissions" color="blue" :key="i">{{ i }}</a-tag>
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import { editUser, updateUser } from '@/api/admin-users'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'

export default {
  components: {
    PageContent,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      form: {
        name: '',
        avatar: '',
        password: '',
        password_confirmation: '',
      },
      profile: {},
      errors: {},
    }
  },
  methods: {
    async getData() {
      const { data } = await editUser()
      this.profile = data
      return data
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
