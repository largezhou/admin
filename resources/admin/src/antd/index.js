import Vue from 'vue'
import Antd from 'ant-design-vue'
import { requireAll } from '@/libs/utils'

Vue.use(Antd)

requireAll(require.context('./', false, /\.js$/))
