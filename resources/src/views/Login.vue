<template>
  <div class="login">
    <div class="login-wrap">
      <Card icon="md-log-in" :title="`${config.name}后台管理系统`">
        <div class="form-con">
          <LoginForm/>
        </div>
      </Card>
    </div>
  </div>
</template>

<script>
import LoginForm from '@/components/LoginForm'
import { mapState } from 'vuex'

export default {
  name: 'Login',
  components: {
    LoginForm,
  },
  computed: {
    ...mapState({
      config: (state) => state.config,
    }),
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
