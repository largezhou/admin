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
    <lz-form-item
      v-for="i of configs"
      :key="i.id"
      :prop="i.slug"
      :label="i.name"
      :tip="i.desc"
    >
      <type-input
        v-model="form[i.slug]"
        :type="i.type"
        :options="i.options"
      />
    </lz-form-item>
    <template #footer-append>
      <a-checkbox v-model="cache">更新缓存</a-checkbox>
    </template>
  </lz-form>
</template>

<script>
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import {
  cacheConfig,
  getConfigsByCategorySlug,
  updateConfigValues,
} from '@/api/configs'
import TypeInput from './TypeInput'
import { CACHE_AFTER_UPDATE_CONFIG, mapConstants, SYSTEM_BASIC } from '@/libs/constants'
import { stringBool } from '@/libs/utils'
import { axios } from '@/axios/request'

export default {
  name: 'ConfigsForm',
  components: {
    TypeInput,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      form: {},
      errors: {},
      configs: [],
      cache: stringBool(localStorage.getItem(CACHE_AFTER_UPDATE_CONFIG)),
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
      const { data } = await getConfigsByCategorySlug(this.category)

      this.configs = data

      const form = {}
      data.forEach((i) => {
        form[i.slug] = i.value
      })
      this.form = form
    },
    async onSubmit() {
      const { data } = await updateConfigValues(this.category, this.form)
      if (this.category === SYSTEM_BASIC.SLUG) {
        this.$store.commit('SET_CONFIG', {
          path: SYSTEM_BASIC.SLUG,
          value: data,
        })
      }

      if (this.cache) {
        await cacheConfig()
      }
    },
  },
  watch: {
    cache(newVal) {
      localStorage.setItem(CACHE_AFTER_UPDATE_CONFIG, newVal)
    },
  },
}
</script>
