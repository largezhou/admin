<template>
  <lz-form
    ref="form"
    :get-data="getData || defaultGetDataFunction"
    :submit="onSubmit"
    :errors.sync="errors"
    :form.sync="form"
    disable-stay
    disable-redirect
    edit-mode
  >
    <el-form-item
      v-for="i of configs"
      :key="i.id"
      :prop="i.slug"
      :label="i.name"
    >
      <type-input
        v-model="form[i.slug]"
        :type="i.type"
        :options="i.options"
      />
    </el-form-item>
  </lz-form>
</template>

<script>
import _last from 'lodash/last'
import LzForm from '@c/LzForm'
import {
  getConfigsByCategorySlug,
  updateConfigValues,
} from '@/api/configs'
import TypeInput from '@v/configs/TypeInput'
import { mapConstants } from '@/libs/constants'

export default {
  name: 'ConfigsForm',
  components: {
    TypeInput,
    LzForm,
  },
  data() {
    return {
      form: {},
      errors: {},
      configs: [],
    }
  },
  computed: {
    ...mapConstants('CONFIG_TYPES'),
  },
  props: {
    category: {
      type: String,
      default() {
        return _last(this.$route.path.split('/')).replace('-', '_')
      },
    },
    getData: Function,
  },
  methods: {
    async defaultGetDataFunction() {
      if (!this.category) {
        throw new Error('必须设置有效的配置分类名称 [ category ]')
      }

      const { data } = await getConfigsByCategorySlug(this.category)

      this.configs = data

      const form = {}
      data.forEach((i) => {
        form[i.slug] = i.value
      })
      this.form = form
    },
    async onSubmit() {
      const form = this.handleFormData()
      await updateConfigValues(form)
    },
    handleFormData() {
      let form = { ...this.form }
      this.configs.forEach((i) => {
        // 如果是文件类型，要把值的对象处理成 path 字符串
        if (i.type === this.CONFIG_TYPES.FILE) {
          let value = form[i.slug]
          if (i.options.max > 1) {
            value = value.map((i) => i.path)
          } else {
            value = value ? value.path : null
          }
          form[i.slug] = value
        }
      })
      return form
    },
  },
}
</script>
