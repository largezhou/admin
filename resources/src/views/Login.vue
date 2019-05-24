<template>
  <div class="login">
    <div class="login-wrap">
      <Card icon="md-log-in" title="后台">
        <div class="form-con">
          <LoginForm/>
        </div>
      </Card>
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
  width: 300px;
  margin: auto;
  padding-top: 30vh;
}
</style>
