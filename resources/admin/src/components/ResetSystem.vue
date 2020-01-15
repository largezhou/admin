<template>
  <loading-action plain type="danger" :action="onReset">重置系统</loading-action>
</template>

<script>
import Request from '@/plugins/request'

export default {
  name: 'ResetSystem',
  methods: {
    async onReset() {
      if (confirm('确认重置？')) {
        await Request.post('/demo/reset-system')
        location.reload()
      }
    },
  },
}
</script>
