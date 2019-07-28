<template>
  <div id="admin-app" v-resize="onResize">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',
  computed: {
    miniWidth() {
      return this.$store.state.miniWidth
    },
  },
  methods: {
    onResize() {
      const mini = window.innerWidth <= 768
      if (mini !== this.miniWidth) {
        mini && this.$store.commit('SET_OPENED', false)
        this.$store.commit('SET_MINI_WIDTH', mini)
      }
    },
  },
}
</script>
