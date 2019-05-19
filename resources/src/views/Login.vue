<template>
  <div>
    <h1>Login</h1>
    <i-button @click="login">登录</i-button>
  </div>
</template>

<script>
import { setToken } from '@/libs/token'

export default {
  name: 'Login',
  methods: {
    login() {
      setToken('token')
      this.$router.push('/')
    },
  },
}
</script>
