export default {
  name: 'LzKeepAlive',
  methods: {
    match(name) {
      return this.$store.state.include.some((i) => {
        if (i instanceof RegExp) {
          return i.test(name)
        } else {
          return i === name
        }
      })
    },
  },
  render(h) {
    const slot = this.$slots.default
    const vnode = slot[0]

    const componentOptions = vnode.componentOptions

    if (componentOptions) {
      const name = componentOptions.Ctor.options.name
      if (!this.match(name)) {
        return vnode
      }

      const cache = this.$store.state.cache
      if (cache[name]) {
        vnode.componentInstance = cache[name].componentInstance
      } else {
        this.$store.commit('ADD_CACHE', { name, vnode })
      }

      vnode.data.keepAlive = true
    }

    return vnode
  },
}
