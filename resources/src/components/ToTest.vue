<template>
  <button-link
    v-if="isDev"
    to="/test/test"
    style="color: red; font-weight: bolder;"
  >
    TEST
  </button-link>
</template>

<script>
import ButtonLink from '@c/ButtonLink'

export default {
  name: 'ToTest',
  components: {
    ButtonLink,
  },
  computed: {
    isDev() {
      return process.env.NODE_ENV === 'development'
    },
  },
}
</script>
