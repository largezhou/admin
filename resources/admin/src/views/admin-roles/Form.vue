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
        :form.sync="form"
        :errors.sync="errors"
        :edit-mode="editMode"
      >
        <el-form-item label="标识" required prop="slug">
          <el-input v-model="form.slug"/>
        </el-form-item>
        <el-form-item label="名称" required prop="name">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="权限" prop="permissions">
          <el-transfer
            filterable
            :filter-method="filterMethod"
            filter-placeholder="搜索权限"
            :titles="['待选', '已选']"
            :button-texts="['移除', '选择']"
            v-model="form.permissions"
            :data="perms"
            :props="{ key: 'id', label: 'name' }"
          />
        </el-form-item>
      </lz-form>
    </el-row>
  </el-card>
</template>

<script>
import LzForm from '@c/LzForm'
import { editAdminRole, updateAdminRole, storeAdminRole, createAdminRole } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'
import FormHelper from '@c/LzForm/FormHelper'

export default {
  name: 'Form',
  components: {
    LzForm,
  },
  mixins: [
    FormHelper,
  ],
  data() {
    return {
      form: {
        slug: '',
        name: '',
        permissions: [],
      },
      errors: {},
      perms: [],
    }
  },
  methods: {
    async getData() {
      let data

      if (this.editMode) {
        ({ data } = await editAdminRole(this.resourceId))
        data.role.permissions = data.role.permissions.map(i => i.id)
        this.fillForm(data.role)
      } else {
        ({ data } = await createAdminRole())
      }

      this.perms = data.permissions
    },
    async onSubmit() {
      if (this.editMode) {
        await updateAdminRole(this.resourceId, this.form)
      } else {
        await storeAdminRole(this.form)
      }
    },
    filterMethod(query, item) {
      return item.name.indexOf(query) > -1
    },
  },
}
</script>

<style scoped lang="scss">
::v-deep {
  .el-transfer-panel__body {
    height: 300px;
  }

  .el-transfer-panel__list.is-filterable {
    height: 249px;
  }
}
</style>
