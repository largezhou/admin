<template>
  <div id="admin-app">
    <p>
      If iView is successfully added to this project, you'll see an
      <code v-text="'<Button>'"></code>
      below
    </p>
    <i-button type="primary">Button</i-button>
    <router-link to="about">About</router-link>
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'app',
}
</script>

<style>
#admin-app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
