<template>
  <component v-if="!loading && component" :is="component"/>
  <h1 v-else>LOADING...</h1>
</template>

<script>
import imports from '@/router/imports'

const getData = () => {
  return new Promise(resolve => {
    setTimeout(() => {
      resolve(Math.random() > 0.4 ? 'user/create' : 'no')
    }, 500)
  })
}

export default {
  name: 'ContentView',
  data: () => ({
    key: '',
    loading: true,
  }),
  computed: {
    component() {
      if (!this.key) {
        return ''
      }

      return imports[this.key] || {
        name: 'pageNotFound',
        render: h => h('h1', 'PAGE NOT FOUND'),
      }
    },
  },
  created() {
    log('content view created')
  },
  async beforeRouteEnter(to, from, next) {
    log('enter')
    const key = await getData()
    log('done')
    next(vm => {
      vm.key = key
      vm.loading = false
    })
  },
  beforeRouteUpdate(to, from, next) {
    log('update')
    log('done')
    next()
  },
  beforeRouteLeave(to, from, next) {
    this.loading = true
    next()
  },
}
</script>
