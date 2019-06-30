<template>
  <el-button
    :type="type"
    v-bind="$attrs"
    class="link"
    :disabled="disabled"
  >
    <el-popover
      placement="top"
      width="160"
      v-model="visible"
      :disabled="disabled"
    >
      <p>{{ notice }}</p>
      <div style="text-align: right; margin: 0">
        <el-button size="mini" @click="onCancel">取消</el-button>
        <loading-action :type="confirmType" size="mini" :action="action">确定</loading-action>
      </div>
      <a slot="reference">
        <slot/>
      </a>
    </el-popover>
  </el-button>
</template>

<script>
export default {
  name: 'PopConfirm',
  data() {
    return {
      visible: false,
    }
  },
  props: {
    notice: {
      type: String,
      default: '确认操作？',
    },
    confirmType: {
      type: String,
      default: 'primary',
    },
    confirm: Function,
    type: String,
    disabled: Boolean,
  },
  methods: {
    onCancel() {
      this.visible = false
      this.$emit('cancel')
    },
    async action() {
      await this.confirm()
      this.visible = false
    },
  },
}
</script>
