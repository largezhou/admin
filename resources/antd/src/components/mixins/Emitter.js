function broadcast(componentName, eventName, params) {
  this.$children.forEach(child => {
    const name = child.$options.componentName

    if (name === componentName) {
      child.$emit.apply(child, [eventName].concat(params))
    } else {
      broadcast.apply(child, [componentName, eventName].concat([params]))
    }
  })
}

export default {
  methods: {
    /**
     * 给指定名称的第一个父组件触发一个自定义事件
     *
     * @param {string} componentName
     * @param {string} eventName
     * @param {*=} params
     */
    dispatch(componentName, eventName, params) {
      let parent = this.$parent || this.$root
      let name = parent.$options.componentName

      while (parent && (!name || name !== componentName)) {
        parent = parent.$parent

        if (parent) {
          name = parent.$options.componentName
        }
      }
      if (parent) {
        parent.$emit.apply(parent, [eventName].concat(params))
      }
    },
    /**
     * 给指定名称的第一个子组件触发一个自定义事件
     *
     * @param {string} componentName
     * @param {string} eventName
     * @param {*=} params
     */
    broadcast(componentName, eventName, params) {
      broadcast.call(this, componentName, eventName, params)
    },
  },
}
