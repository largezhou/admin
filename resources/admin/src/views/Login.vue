<template>
  <div class="login">
    <div class="login-wrap">
      <el-card>
        <template #header>
          <span>{{ appName }}</span>
        </template>
        <login-form @keydown.enter.native="$refs.submit.onAction" ref="form"/>
        <loading-action
          ref="submit"
          class="login-btn"
          type="primary"
          :action="onLogin"
        >
          登录
        </loading-action>
      </el-card>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  computed: {
    appName() {
      return this.$store.getters.appName
    },
  },
  methods: {
    async onLogin() {
      await this.$refs.form.onSubmit()
      this.$router.push(this.$route.query.redirect || '/')
    },
  },
}
</script>

<style scoped lang="scss">
.login {
  width: 100%;
  height: 100%;
}

.login-wrap {
  width: 350px;
  margin: auto;
  padding-top: 30vh;
}

.login-btn {
  width: 100%;
}

::v-deep {
  .el-card__header {
    text-align: center;
  }
}
</style>
