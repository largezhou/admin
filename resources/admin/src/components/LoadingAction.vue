<template>
  <component
    :is="comp"
    :type="type"
    v-bind="$attrs"
    v-on="$listeners"
    @click="onAction"
    :loading="actionLoading || loading"
    :disabled="disabled"
  >
    <slot/>
  </component>
</template>

<script>
export default {
  name: 'LoadingAction',
  data: () => ({
    actionLoading: false,
    disabled: false,
  }),
  props: {
    /**
     * 要有效的使用 loading 效果，这里必须传入一个异步函数
     */
    action: {
      type: Function,
      required: true,
    },
    /**
     * 作为什么组件
     */
    comp: {
      type: String,
      default: 'el-button',
    },
    /**
     * 这个 type props 是为了兼容 Button 组件中，type 是用来设置颜色的
     * 这里不加一个 type 的话，如果 Button 组件设置了 type=primary
     * 那渲染成的 html button 的 type 也是 primary，点击后会提交表单
     */
    type: String,
    loading: Boolean,
    /**
     * action 执行成功后，继续禁用按钮按钮一小段时间，避免连续点击
     * 当执行成功后，需要跳转页面或者关闭弹窗之类的，可以设置为 true
     * 设为 <= 0，则不会禁用
     */
    disableOnSuccess: {
      type: [String, Number],
      default: 500,
    },
  },
  beforeDestroy() {
    this.clearRecoverDisabledTimeout()
  },
  methods: {
    async onAction() {
      if (this.actionLoading || this.disabled) {
        return false
      }
      this.actionLoading = true
      try {
        await this.action()
        this.handleDisableOnSuccess()
      } catch (e) {
        Promise.reject(e)
      }
      this.actionLoading = false
    },
    handleDisableOnSuccess() {
      if (this.disableOnSuccess > 0) {
        this.disabled = true
        this.clearRecoverDisabledTimeout()
        this.recoverDisabledTimeout = setTimeout(() => {
          this.disabled = false
        }, this.disableOnSuccess)
      }
    },
    clearRecoverDisabledTimeout() {
      this.recoverDisabledTimeout && clearTimeout(this.recoverDisabledTimeout)
    },
  },
}
</script>
