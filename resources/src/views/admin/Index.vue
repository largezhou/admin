<template>
  <div>
    <h1>Index</h1>
    <i-button @click="logout">退出登录</i-button>
  </div>
</template>

<script>
export default {
  name: 'Index',
  methods: {
    async logout() {
      try {
        await this.$store.dispatch('logout')
        this.$Message.success('已安全退出')
        this.$router.push({ name: 'login' })
      } catch (e) {
        const res = e.response
        if (res && res.status === 401) {
          this.$router.push({ name: 'login' })
        }
      }
    },
  },
}
</script>
