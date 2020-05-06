<template>
  <a-popover
    trigger="click"
    :visible="visible"
    v-bind="$attrs"
    @visibleChange="onVisibleChange"
  >
    <template #content>
      <div class="mb-2">
        <span v-if="title">{{ title }}</span>
        <slot v-else name="title"/>
      </div>
      <space class="actions">
        <a-button size="small" @click="onCancel">{{ cancelText }}</a-button>
        <loading-action
          :action="onConfirm"
          size="small"
          type="primary"
        >
          {{ okText }}
        </loading-action>
      </space>
    </template>
    <slot v-bind:pop="this"/>
  </a-popover>
</template>

<script>
import Space from '@c/Space'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'LzPopconfirm',
  components: {
    LoadingAction,
    Space,
  },
  data: () => ({
    visible: false,
  }),
  props: {
    title: {
      type: String,
      default: '确认操作？',
    },
    cancelText: {
      type: String,
      default: '取消',
    },
    okText: {
      type: String,
      default: '确认',
    },
    /**
     * 传入异步函数，就会有 loading 效果
     */
    confirm: Function,
    disabled: Boolean,
  },
  methods: {
    onCancel() {
      this.visible = false
      this.$emit('cancel')
    },
    async onConfirm() {
      this.confirm && await this.confirm()
      this.$emit('confirm')
      this.visible = false
    },
    onVisibleChange(visible) {
      if (this.disabled) {
        return
      }
      this.visible = visible
    },
  },
}
</script>

<style scoped lang="less">
.actions {
  display: flex;
  justify-content: flex-end;
  min-width: 130px;
}
</style>
