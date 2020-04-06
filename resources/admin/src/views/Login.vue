<template>
  <div class="login" :style="styles">
    <div class="login-wrap">
      <el-card>
        <template #header>
          <span>{{ appName }}</span>
        </template>
        <div class="pb-3 accounts">
          <div>点击名字自动填入帐号密码</div>
          <div><span @click="fillAccount('admin')">管理员</span>：admin/000000</div>
          <div><span @click="fillAccount('demo')">普通用户</span>：demo/000000</div>
        </div>
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
    fillAccount(account) {
      this.$refs.form.form = {
        username: account,
        password: '000000',
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

.login-btn {
  width: 100%;
}

::v-deep {
  .el-card__header {
    text-align: center;
  }
}

.accounts {
  div {
    color: #5d5d5d;
    line-height: 30px;
  }

  span {
    color: #409EFF;
    cursor: pointer;
  }
}
</style>
