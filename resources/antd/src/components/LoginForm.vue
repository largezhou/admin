<template>
  <a-form>
    <a-form-item
      :help="errors.username"
      :validate-status="errorStatus('username')"
    >
      <a-input
        autocomplete="off"
        v-model="form.username"
        placeholder="帐号"
      >
        <svg-icon slot="prefix" icon-class="user"/>
      </a-input>
    </a-form-item>
    <a-form-item
      :help="errors.password"
      :validate-status="errorStatus('password')"
    >
      <a-input
        autocomplete="off"
        type="password"
        v-model="form.password"
        placeholder="密码"
      >
        <svg-icon slot="prefix" icon-class="password"/>
      </a-input>
    </a-form-item>
  </a-form>
</template>

<script>
import { getMessage } from '@/libs/utils'

export default {
  name: 'LoginForm',
  data: () => ({
    form: {
      username: '',
      password: '',
    },
    errors: {},
  }),
  methods: {
    async onSubmit() {
      await this.$store.dispatch('login', this)
      this.$message.success(getMessage('loggedIn'))
    },
    errorStatus(key) {
      return this.errors[key] ? 'error' : null
    },
  },
}
</script>
