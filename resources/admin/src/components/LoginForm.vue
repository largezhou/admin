<template>
  <lz-form
    :form.sync="form"
    :errors.sync="errors"
    in-dialog
    :footer="false"
  >
    <lz-form-item prop="username">
      <a-input
        ref="username"
        v-model="form.username"
        placeholder="帐号"
      >
        <svg-icon slot="prefix" icon-class="user"/>
      </a-input>
    </lz-form-item>
    <lz-form-item prop="password">
      <a-input
        type="password"
        v-model="form.password"
        placeholder="密码"
      >
        <svg-icon slot="prefix" icon-class="password"/>
      </a-input>
    </lz-form-item>
  </lz-form>
</template>

<script>
import { getMessage } from '@/libs/utils'
import LzForm from '@c/LzForm/index'
import LzFormItem from '@c/LzForm/LzFormItem'

export default {
  name: 'LoginForm',
  components: {
    LzFormItem,
    LzForm,
  },
  data: () => ({
    form: {
      username: '',
      password: '',
    },
    errors: {},
  }),
  mounted() {
    this.$refs.username.focus()
  },
  methods: {
    async onSubmit() {
      await this.$store.dispatch('login', this)
      this.$message.success(getMessage('loggedIn'))
    },
  },
}
</script>
