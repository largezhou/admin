/**
 * 这里主要是对 antd 自带的组件，做一些修改
 */

import Vue from 'vue'
import Antd from 'ant-design-vue'
import { requireAll } from '@/libs/utils'

Vue.use(Antd)

requireAll(require.context('./', false, /\.js$/))
