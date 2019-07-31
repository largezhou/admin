<template>
  <div class="login" :style="styles">
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
import { getUrl } from '@/libs/utils'
export default {
  name: 'Login',
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
        background: `url(${getUrl(url)}) no-repeat center center`,
        backgroundSize: 'cover',
      }
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

/deep/ {
  .el-card__header {
    text-align: center;
  }
}
</style>
