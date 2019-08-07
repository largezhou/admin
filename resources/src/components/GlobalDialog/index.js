import Vue from 'vue'
import { Dialog } from 'element-ui'
import store from '@/store'
import router from '@/router'

const GlobalDialog = Vue.extend({
  name: 'GlobalDialog',
  data() {
    return {
      visible: false,
    }
  },
  props: {
    ...Dialog.props,
    content: [String, Function],
    footer: Function,
    on: Object,
    directives: Array,
  },
  mounted() {
    this.visible = true
  },
  methods: {
    clean() {
      this.$destroy()
      const parentNode = this.$el.parentNode
      parentNode && parentNode.removeChild(this.$el)
    },
  },
  watch: {
    $route() {
      this.visible = false
    },
  },
  render(h) {
    let { content, footer } = this
    if (content) {
      content = (typeof this.content === 'string')
        ? this.content
        : this.content(h)
    }
    footer = footer && (<template slot="footer">{this.footer(h)}</template>)

    return h('el-dialog', {
      props: {
        ...this.$props,
        visible: this.visible,
      },
      on: {
        ...this.on,
        closed: this.clean,
        'update:visible': (val) => (this.visible = val),
      },
      directives: this.directives,
      ref: 'globalDialog',
    }, [content, footer])
  },
})

/**
 * 实例化组件
 *
 * @param propsData
 * @returns {*}
 */
GlobalDialog.new = (propsData) => {
  const vm = new GlobalDialog({
    store,
    router,
    propsData,
  })

  document.body.appendChild(vm.$mount().$el)

  return vm
}

export default GlobalDialog
