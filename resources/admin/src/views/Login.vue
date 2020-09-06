<template>
  <div class="login" :style="styles">
    <a-card class="login-card" :title="appName">
      <div class="pb-1 accounts">
        <div>点击名字自动填入帐号密码</div>
        <div><span @click="fillAccount('admin')">管理员</span>：admin/000000</div>
        <div><span @click="fillAccount('demo')">普通用户</span>：demo/000000</div>
      </div>
      <login-form
        ref="form"
        @keydown.enter.native="$refs.login.onAction"
      />
      <space direction="vertical" class="w-100">
        <reset-system class="w-100"/>
        <loading-action
          ref="login"
          type="primary"
          class="w-100"
          :action="onLogin"
          disable-on-success="2000"
        >
          <span>登录</span>
        </loading-action>
      </space>
    </a-card>
  </div>
</template>

<script>
import LoginForm from '@c/LoginForm'
import LoadingAction from '@c/LoadingAction'
import { getUrl } from '@/libs/utils'
import Space from '@c/Space'
import ResetSystem from '@c/ResetSystem'

export default {
  name: 'Login',
  components: {
    ResetSystem,
    Space,
    LoadingAction,
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
        background: `url(${getUrl(url)}) no-repeat center center`,
        backgroundSize: 'cover',
      }
    },
  },
  methods: {
    async onLogin() {
      await this.$refs.form.onSubmit()
      this.$router.push(this.$route.query.redirect || '/index')
    },
    fillAccount(account) {
      this.$refs.form.form = Object.assign({}, this.$refs.form.form, {
        username: account,
        password: '000000',
      })
    },
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/vars";

.login {
  height: 100vh;
  display: flex;
}

.login-card {
  width: 300px;
  margin: 20vh auto auto auto;
}

.accounts {
  div {
    line-height: 30px;
  }

  span {
    color: @blue-6;
    cursor: pointer;
  }
}
</style>
