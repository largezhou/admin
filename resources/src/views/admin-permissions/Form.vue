<template>
  <el-card>
    <template v-slot:header>
      <span>添加权限</span>
    </template>
    <el-form ref="form" :model="form" label-width="100px">
      <el-form-item
        label="标识"
        required
        :error="errors.slug"
        prop="slug"
      >
        <el-input v-model="form.slug"/>
      </el-form-item>
      <el-form-item
        label="名称"
        required
        :error="errors.name"
        prop="name"
      >
        <el-input v-model="form.name"/>
      </el-form-item>
      <el-form-item
        label="方法"
        :error="errors.http_method"
        prop="http_method"
      >
        <el-select v-model="form.http_method" multiple>
          <el-option
            v-for="i in methods"
            :key="i"
            :label="i"
            :value="i"
          />
        </el-select>
      </el-form-item>
      <el-form-item
        label="名称"
        :error="errors.http_path"
        prop="http_path"
      >
        <el-input :autosize="{ minRows: 6 }" type="textarea" v-model="form.http_path"/>
      </el-form-item>
      <el-form-item>
        <loading-action type="primary" :action="onSubmit">{{ editMode ? '更新' : '添加' }}</loading-action>
        <el-button @click="onReset">重置</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>

<script>
import { assignExsits, handleValidateErrors } from '@/libs/utils'
import { editAdminPerm, storeAdminPerm, updateAdminPerm } from '@/api/admin-perms'

export default {
  name: 'Form',
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
  computed: {
    editMode() {
      return !!this.permId
    },
    permId() {
      return this.$route.params.id
    },
  },
  created() {
    if (this.editMode) {
      this.editAdminPerm()
    }
  },
  methods: {
    async onSubmit() {
      this.errors = {}
      try {
        this.editMode
          ? await this.updateAdminPerm()
          : await this.storeAdminPerm()
      } catch (e) {
        this.errors = handleValidateErrors(e.response)
      }
    },
    async updateAdminPerm() {
      await updateAdminPerm(this.permId, this.form)
      this.$router.back()
    },
    async storeAdminPerm() {
      await storeAdminPerm(this.form)
      this.$router.push('/admin-permissions')
    },
    async editAdminPerm() {
      const { data } = await editAdminPerm(this.permId)
      this.form = assignExsits(this.form, data)
      await this.$nextTick()
      this.$refs.form.setInitialValues()
    },
    onReset() {
      this.$refs.form.resetFields()
    },
  },
}
</script>
