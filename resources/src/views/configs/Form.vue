<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :get-data="getData"
        :submit="onSubmit"
        :errors.sync="errors"
        :form.sync="form"
        :edit-mode="editMode"
      >
        <el-form-item label="类型" required prop="type">
          <el-radio-group v-model="form.type">
            <el-radio
              v-for="i of types"
              :key="i.value"
              :label="i.value"
            >
              {{ i.label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="分类" required prop="category_id">
          <el-select
            v-model="form.category_id"
            placeholder="选择分类"
            filterable
          >
            <el-option
              v-for="i of cates"
              :key="i.id"
              :label="i.name"
              :value="i.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="名称" required prop="name">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="标识" required prop="slug">
          <el-input v-model="form.slug"/>
        </el-form-item>
        <el-form-item label="简介" prop="desc">
          <el-input v-model="form.desc" type="textarea"/>
        </el-form-item>
        <el-form-item label="选项" prop="options">
          <type-options v-model="form.options" :type="form.type"/>
        </el-form-item>
        <el-form-item label="值" prop="value">
          <type-input
            v-model="form.value"
            :type="form.type"
            :options="form.options"
          />
        </el-form-item>
        <el-form-item label="验证规则" prop="validation_rules">
          <el-input v-model="form.validation_rules"/>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>

<script>
import LzForm from '@c/LzForm'
import {
  createConfig,
  editConfig,
  getConfigCategories, storeConfig, updateConfig,
} from '@/api/configs'
import FormHelper from '@c/LzForm/FormHelper'
import _forIn from 'lodash/forIn'
import TypeOptions from '@v/configs/TypeOptions'
import TypeInput from '@v/configs/TypeInput'
import { toInt } from '@/libs/utils'

export default {
  name: 'Form',
  components: {
    TypeInput,
    TypeOptions,
    LzForm,
  },
  mixins: [
    FormHelper,
  ],
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
    async getData() {
      let data

      if (this.editMode) {
        ({ data } = await editConfig(this.resourceId))
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

      this.editMode
        ? this.fillForm(data.config)
        : this.form.category_id = toInt(this.$route.query.category_id, '')
    },
    async onSubmit() {
      if (this.editMode) {
        await updateConfig(this.resourceId, this.form)
      } else {
        await storeConfig(this.form)
      }
    },
  },
}
</script>
