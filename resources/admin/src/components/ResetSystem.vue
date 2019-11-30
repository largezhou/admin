<template>
  <loading-action plain type="danger" :action="onReset">重置系统</loading-action>
</template>

<script>
import axios from '@/plugins/axios'

export default {
  name: 'ResetSystem',
  methods: {
    async onReset() {
      if (confirm('确认重置？')) {
        await axios.post('/demo/reset-system')
        location.reload()
      }
    },
  },
}
</script>
