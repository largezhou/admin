import Vue from 'vue'
import ElementUI from 'element-ui'
import './index.scss'

import Form from './components/Form'
import FormItem from './components/FormItem'

Vue.use(ElementUI, {
  size: 'medium',
})

const strats = Vue.config.optionMergeStrategies
// 备份原来的
const mounted = strats.mounted
// mounted 覆盖合并
strats.mounted = (toVal, fromVal) => fromVal

Vue.component(FormItem.name, FormItem)
Vue.component(Form.name, Form)
