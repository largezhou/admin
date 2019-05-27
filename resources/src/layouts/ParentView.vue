<template>
  <router-view/>
</template>

<script>
export default {
  name: 'ParentView',
  created() {
    log('parent view created')
  },
}
</script>
