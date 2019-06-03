<template>
  <div class="navbar-items">
    <slot/>
  </div>
</template>

<script>
export default {
  name: 'NavbarItems',
}
</script>

<style scoped>
.navbar-items {
  display: flex;
  max-width: 100%;
  padding: 0;
  height: inherit;
}
</style>
