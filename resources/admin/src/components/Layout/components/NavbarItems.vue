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

<style scoped lang="scss">
.navbar-items {
  display: flex;
  max-width: 100%;
  padding: 0;
  height: inherit;

  > * {
    display: flex;
    align-items: center;
    padding: 0 20px;
  }

  .el-button {
    padding: 0 20px;
    border: none;
    border-radius: 0;
    box-shadow: none;

    + .el-button {
      margin-left: 0;
    }
  }
}
</style>
