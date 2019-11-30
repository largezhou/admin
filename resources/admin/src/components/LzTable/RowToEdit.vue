<template>
  <button-link size="small" :to="link">
    <slot>编辑</slot>
  </button-link>
</template>

<script>
import ButtonLink from '@c/ButtonLink'
import Mixin from './Mixin'

export default {
  name: 'RowToEdit',
  components: {
    ButtonLink,
  },
  mixins: [
    Mixin,
  ],
  data() {
    return {
      identify: undefined,
    }
  },
  computed: {
    link() {
      this.checkResourceName()

      return `/${this.resource}/${this.identify}/edit`
    },
  },
  mounted() {
    this.setIdentify()
  },
}
</script>
