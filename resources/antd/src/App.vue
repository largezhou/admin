<template>
  <a-config-provider :locale="locale">
    <div id="admin-app" class="h-100" v-resize="onResize">
      <router-view/>
    </div>
  </a-config-provider>
</template>

<script>
import zhCN from 'ant-design-vue/es/locale/zh_CN'

export default {
  name: 'App',
  data: () => ({
    locale: zhCN,
  }),
  computed: {
    miniWidth() {
      return this.$store.state.miniWidth
    },
    tinyWidth() {
      return this.$store.state.tinyWidth
    },
  },
  methods: {
    onResize() {
      const mini = window.innerWidth <= 768
      if (mini !== this.miniWidth) {
        mini && this.$store.commit('SET_OPENED', false)
        this.$store.commit('SET_MINI_WIDTH', mini)
      }
      const tiny = window.innerWidth <= 575
      if (tiny !== this.tinyWidth) {
        this.$store.commit('SET_TINY_WIDTH', tiny)
      }
    },
  },
}
</script>
