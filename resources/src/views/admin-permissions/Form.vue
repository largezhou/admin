<template>
  <el-card>
    <template v-slot:header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :edit-method="editAdminPerm"
        :store-method="storeAdminPerm"
        :update-method="updateAdminPerm"
        redirect="/admin-permissions"
        :form.sync="form"
        :errors.sync="errors"
      >
        <el-form-item label="标识" required prop="slug">
          <el-input v-model="form.slug"/>
        </el-form-item>
        <el-form-item label="名称" required prop="name">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="方法" prop="http_method">
          <el-select
            v-model="form.http_method"
            clearable
            multiple
            placeholder="选择请求方法"
          >
            <el-option
              v-for="i in methods"
              :key="i"
              :label="i"
              :value="i"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="路径" prop="http_path">
          <el-input :autosize="{ minRows: 6 }" type="textarea" v-model="form.http_path"/>
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>

<script>
import { editAdminPerm, storeAdminPerm, updateAdminPerm } from '@/api/admin-perms'
import LzForm from '@c/LzForm'

export default {
  name: 'Form',
  components: {
    LzForm,
  },
  data() {
    return {
      form: {
        slug: '',
        name: '',
        http_method: '',
        http_path: '',
      },
      errors: {},
      methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'],
    }
  },
  methods: {
    async editAdminPerm(id) {
      const res = await editAdminPerm(id)
      res.data.http_path = res.data.http_path.join('\n')
      return res
    },
    storeAdminPerm,
    updateAdminPerm,
  },
}
</script>
