<script>
import { Form } from 'ant-design-vue'

export default {
  name: 'LzFormItem',
  extends: Form.Item,
  props: {
    /**
     * 以 tooltip 的形式展示提示文字
     */
    tip: String,
    /**
     * 表单中的 name 值
     */
    prop: String,
  },
  methods: {
    renderLabel() {
      const vnode = Form.Item.methods.renderLabel.bind(this)(...arguments)
      if (this.tip) {
        vnode.componentOptions.children.push((
          <a-tooltip placement="topLeft" class="ml-1">
            <span slot="title" domPropsInnerHTML={this.tip.replace('\n', '<br>')}/>
            <a-icon type="question-circle"/>
          </a-tooltip>
        ))
      }
      return vnode
    },
  },
}
</script>

<style scoped lang="less">
@import "~ant-design-vue/lib/style/color/colors.less";

.ant-form-item-label {
  &::after {
    content: '';
    position: relative;
    top: -0.5px;
    margin: 0 8px 0 2px;
  }

  .anticon {
    color: @blue-6;
  }
}

.ant-form-item-label > label::after {
  content: '';
  margin: 0;
}
</style>
