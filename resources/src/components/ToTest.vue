<template>
  <el-button
    v-if="isDev"
    class="link"
    style="color: red; font-weight: bolder;"
  >
    <router-link to="/test/test">TEST</router-link>
  </el-button>
</template>

<script>
export default {
  name: 'ToTest',
  computed: {
    isDev() {
      return process.env.NODE_ENV === 'development'
    },
  },
}
</script>
