<template>
  <el-switch
    ref="input"
    v-bind="$attrs"
    v-on="$listeners"
    @change="onSubmit"
    :disabled="loading"
  />
</template>

<script>
import Mixin from './Mixin'

export default {
  name: 'SwitchEdit',
  mixins: [
    Mixin,
  ],
  data: () => ({
    resetValueWhenError: true,
  }),
  methods: {
    onSubmit(val) {
      this.goUpdate(val)
    },
  },
}
</script>
