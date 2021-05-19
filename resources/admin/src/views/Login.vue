<template>
  <div class="login">
    <a-card class="login-card" :title="appName">
      <login-form
        ref="form"
        @keydown.enter.native="$refs.login.onAction"
      />
      <loading-action
        ref="login"
        type="primary"
        class="w-100"
        :action="onLogin"
        disable-on-success="2000"
      >
        <span>登录</span>
      </loading-action>
    </a-card>
  </div>
</template>

<script>
import LoginForm from '@c/LoginForm'
import LoadingAction from '@c/LoadingAction'
import Router from 'vue-router'

export default {
  name: 'Login',
  components: {
    LoadingAction,
    LoginForm,
  },
  computed: {
    appName() {
      return this.$store.getters.appName
    },
  },
  methods: {
    async onLogin() {
      await this.$refs.form.onSubmit()
      this.$router
        .push(this.$route.query.redirect || '/')
        .catch((e) => {
          if (!Router.isNavigationFailure(e)) {
            Promise.reject(e)
          }
        })
    },
  },
}
</script>

<style scoped lang="less">
.login {
  height: 100vh;
  display: flex;
}

.login-card {
  width: 300px;
  margin: 30vh auto auto auto;
}
</style>
