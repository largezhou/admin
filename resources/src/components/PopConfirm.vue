<template>
  <component
    :is="comp"
    :type="type"
    v-bind="$attrs"
    :disabled="disabled"
    class="pop-confirm"
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
        <loading-action
          :type="confirmType"
          size="mini"
          :action="action"
          :disabled="disabled"
        >
          确定
        </loading-action>
      </div>
      <span class="trigger" slot="reference"/>
    </el-popover>
    <slot/>
  </component>
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
    /**
     * 弹框中，确认按钮的 type 属性
     */
    confirmType: {
      type: String,
      default: 'primary',
    },
    confirm: Function,
    /**
     * 兼容，当 comp 为 el-button 时，，，
     */
    type: String,
    disabled: Boolean,
    /**
     * 要显示成的组件
     */
    comp: {
      type: String,
      default: 'el-button',
    },
  },
  methods: {
    onCancel() {
      this.visible = false
      this.$emit('cancel')
    },
    async action() {
      if (this.disabled) {
        return
      }

      await this.confirm()
      this.visible = false
    },
  },
}
</script>

<style scoped lang="scss">
.pop-confirm {
  position: relative;
}

.trigger {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
}
</style>
