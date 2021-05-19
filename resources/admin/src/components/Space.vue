<script>
export default {
  name: 'Space',
  props: {
    size: {
      type: [Number, String],
      default: 8,
    },
    direction: {
      type: String,
      default: 'horizontal',
      validator: function (value) {
        return ['horizontal', 'vertical'].indexOf(value) !== -1
      },
    },
  },
  methods: {
    isNodeNotVisible(vnode) {
      if (vnode.isComment) {
        return true
      }

      const directives = vnode.data?.directives || []
      let vShow
      let vIf
      directives.forEach((d) => {
        if (d.name === 'if') {
          vIf = d.value
        }
        if (d.name === 'show') {
          vShow = d.value
        }
      })

      return (vShow && !vShow.value) || (vIf && !vIf.value)
    },
  },
  render(h) {
    const slots = (this.$slots.default || []).filter((item) => !this.isNodeNotVisible(item))
    const l = slots.length
    const wrappedSlots = slots.map((item, index) => {
      const marginSide = this.direction === 'horizontal' ? 'marginRight' : 'marginBottom'
      return (
        <div
          class="space-item"
          style={{ [marginSide]: `${index === l - 1 ? 0 : this.size}px` }}
        >{item}</div>
      )
    })

    return (
      <div
        class={{
          space: true,
          vertical: this.direction !== 'horizontal',
        }}
      >{wrappedSlots}</div>
    )
  },
}
</script>

<style scoped lang="less">
.space {
  display: inline-flex;
}

.vertical {
  flex-direction: column;
}
</style>
