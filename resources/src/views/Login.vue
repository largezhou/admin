<template>
  <div class="login" :style="styles">
    <div class="login-wrap">
      <el-card>
        <template #header>
          <span>{{ appName }}</span>
        </template>
        <login-form/>
      </el-card>
    </div>
  </div>
</template>

<script>
import LoginForm from '@c/LoginForm'

export default {
  name: 'Login',
  components: {
    LoginForm,
  },
  computed: {
    appName() {
      return this.$store.getters.appName
    },
    styles() {
      const url = this.$store.getters.getConfig('system_basic.login_background')
      if (!url) {
        return null
      }

      return {
        background: `url(${url}) no-repeat center center`,
        backgroundSize: 'cover',
      }
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

/deep/ {
  .el-card__header {
    text-align: center;
  }
}
</style>
