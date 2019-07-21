<template>
  <div class="login">
    <div class="login-wrap">
      <el-card>
        <template #header>
          <span>登录</span>
        </template>
        <LoginForm/>
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
