<template>
  <el-input-number
    ref="input"
    v-bind="$attrs"
    v-on="$listeners"
    @change="onSubmit"
    :disabled="loading"
  />
</template>

<script>
import Mixin from './Mixin'
import _debounce from 'lodash/debounce'

export default {
  name: 'InputNumberEdit',
  data() {
    return {
      value: this.$attrs.value,
      resetValueWhenError: true,
    }
  },
  mixins: [
    Mixin,
  ],
  created() {
    this.debounceUpdate = _debounce(() => {
      return this.goUpdate(this.value)
    }, 500)
  },
  methods: {
    onSubmit(val) {
      this.value = val
      this.debounceUpdate()
    },
  },
}
</script>
