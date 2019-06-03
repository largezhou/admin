<template>
  <el-switch
    ref="input"
    v-bind="$attrs"
    v-on="$listeners"
    @change="onChange"
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
}
</script>
