<template>
  <lz-form
    ref="form"
    :get-data="getData"
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
      :helper="i.desc"
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
import LzForm from '@c/LzForm'
import {
  getConfigsByCategorySlug,
  updateConfigValues,
} from '@/api/configs'
import TypeInput from '@v/configs/TypeInput'
import { mapConstants, SYSTEM_BASIC } from '@/libs/constants'

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
    ...mapConstants(['CONFIG_TYPES']),
    category() {
      return this.$route.params.categorySlug
    },
  },
  methods: {
    async getData() {
      if (!this.category) {
        throw new Error('无法获取到 [ category ]')
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
      const { data } = await updateConfigValues(this.form)
      this.$store.commit('SET_CONFIG', {
        path: SYSTEM_BASIC.SLUG,
        value: data,
      })
    },
  },
}
</script>
