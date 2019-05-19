<template>
  <div>
    <h1>Index</h1>
    <i-button @click="logout">退出登录</i-button>
  </div>
</template>

<script>
import { removeToken } from '@/libs/token'

export default {
  name: 'Index',
  methods: {
    logout() {
      removeToken()
      this.$router.push('login')
    },
  },
}
</script>
