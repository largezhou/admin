<template>
  <keep-alive :include="$store.state.include">
    <router-view/>
  </keep-alive>
</template>

<script>
export default {
  name: 'ParentView',
}
</script>
