<template>
  <loading-action plain type="danger" :action="onReset">重置系统</loading-action>
</template>

<script>
import Request from '@/axios/request'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'ResetSystem',
  components: {
    LoadingAction,
  },
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
