<template>
  <page-content center>
    <lz-form
      :get-data="getData"
      :submit="onSubmit"
      :form.sync="form"
      :errors.sync="errors"
    >
      <lz-form-item label="类型" prop="type" required>
        <a-radio-group v-model="form.type">
          <a-radio
            v-for="i of types"
            :key="i.value"
            :value="i.value"
          >
            {{ i.label }}
          </a-radio>
        </a-radio-group>
      </lz-form-item>
      <lz-form-item label="分类" prop="category_id" required>
        <a-select
          v-model="form.category_id"
          option-filter-prop="name"
          show-search
        >
          <a-select-option
            v-for="i of cates"
            :key="i.id"
            :name="i.name"
          >
            {{ i.name }}
          </a-select-option>
        </a-select>
      </lz-form-item>
      <lz-form-item label="名称" prop="name" required>
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="标识" prop="slug" required>
        <a-input v-model="form.slug"/>
      </lz-form-item>
      <lz-form-item label="简介" prop="desc">
        <a-input v-model="form.desc" type="textarea"/>
      </lz-form-item>
      <lz-form-item label="选项" prop="options">
        <type-options v-model="form.options" :type="form.type"/>
      </lz-form-item>
      <lz-form-item label="值" prop="value">
        <type-input
          v-model="form.value"
          :type="form.type"
          :options="form.options"
        />
      </lz-form-item>
      <lz-form-item label="验证规则" prop="validation_rules">
        <a-input v-model="form.validation_rules"/>
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import LzForm from '@c/LzForm'
import {
  createConfig,
  editConfig,
  storeConfig,
  updateConfig,
} from '@/api/configs'
import _forIn from 'lodash/forIn'
import TypeOptions from './components/TypeOptions'
import TypeInput from './components/TypeInput'
import { toInt } from '@/libs/utils'
import PageContent from '@c/PageContent'
import LzFormItem from '@c/LzForm/LzFormItem'

export default {
  name: 'Form',
  components: {
    LzFormItem,
    PageContent,
    TypeInput,
    TypeOptions,
    LzForm,
  },
  data() {
    return {
      form: {
        type: '',
        category_id: '',
        name: '',
        slug: '',
        desc: '',
        options: {},
        value: '',
        validation_rules: '',
      },
      errors: {},

      types: [],
      cates: [],
    }
  },
  methods: {
    async getData($form) {
      let data

      if ($form.realEditMode) {
        ({ data } = await editConfig($form.resourceId))
      } else {
        ({ data } = await createConfig())
      }

      this.cates = data.categories

      const types = []
      _forIn(data.types_map, (value, key) => {
        types.push({
          value: key,
          label: value,
        })
      })
      this.types = types
      // 表单中类型，默认选择第一个
      this.form.type = types[0].value

      !this.editMode && (this.form.category_id = toInt(this.$route.query.category_id, ''))

      return data.data
    },
    async onSubmit($form) {
      if ($form.realEditMode) {
        await updateConfig($form.resourceId, this.form)
      } else {
        await storeConfig(this.form)
      }
    },
  },
}
</script>
