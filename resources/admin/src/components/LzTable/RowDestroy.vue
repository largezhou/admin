<template>
  <pop-confirm
    type="danger"
    :confirm="onConfirm"
    size="small"
    v-bind="$attrs"
  >
    <slot>删除</slot>
  </pop-confirm>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import Request from '@/plugins/request'
import { getMessage } from '@/libs/utils'
import Mixin from './Mixin'

export default {
  name: 'RowDestroy',
  components: {
    PopConfirm,
  },
  mixins: [
    Mixin,
  ],
  methods: {
    async onConfirm() {
      this.checkResourceName()

      // 只有真正执行删除确认时，才去获取 identify
      this.setIdentify()

      await Request.delete(`${this.resource}/${this.identify}`)
      // 列表在变，索引要重新获取
      this.data.splice(this.getIndex(), 1)
      this.$message.success(getMessage('destroyed'))
    },
  },
}
</script>
