<template>
  <component
    :is="comp"
    :type="type"
    v-bind="$attrs"
    v-on="$listeners"
    @click="onAction"
    :loading="loading"
  >
    <slot/>
  </component>
</template>

<script>
export default {
  name: 'LoadingAction',
  data: () => ({
    loading: false,
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
  },
  methods: {
    async onAction() {
      if (this.loading) {
        return false
      }
      this.loading = true
      try {
        await this.action()
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
