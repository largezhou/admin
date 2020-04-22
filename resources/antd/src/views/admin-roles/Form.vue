<template>
  <page-content center>
    <lz-form
      ref="form"
      :get-data="getData"
      :submit="onSubmit"
      :form.sync="form"
      :errors.sync="errors"
    >
      <lz-form-item label="标识" required prop="slug">
        <a-input v-model="form.slug"/>
      </lz-form-item>
      <lz-form-item label="名称" required prop="name">
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="权限" prop="permissions">
        <lz-transfer
          :data-source="perms"
          v-model="form.permissions"
        />
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import {
  createAdminRole,
  editAdminRole,
  storeAdminRole,
  updateAdminRole,
} from '@/api/admin-roles'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'
import LzTransfer from '@c/LzForm/LzTransfer'

export default {
  name: 'Form',
  components: {
    LzTransfer,
    PageContent,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      form: {
        slug: '',
        name: '',
        permissions: [],
      },
      perms: [],
      errors: {},
    }
  },
  methods: {
    async getData() {
      let data

      if (this.$refs.form.realEditMode) {
        ({ data } = await editAdminRole(this.$refs.form.resourceId))
        data.data.permissions = data.data.permissions.map(i => i.id)
      } else {
        ({ data } = await createAdminRole())
      }

      this.perms = data.permissions.map((i) => ({ key: i.id.toString(), title: i.name }))

      return data.data
    },
    async onSubmit() {
      if (this.$refs.form.realEditMode) {
        await updateAdminRole(this.$refs.form.resourceId, this.form)
      } else {
        await storeAdminRole(this.form)
      }
    },
  },
}
</script>
