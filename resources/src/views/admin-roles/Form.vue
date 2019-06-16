<template>
  <el-card>
    <template v-slot:header>
      <span>添加角色</span>
    </template>
    <el-row type="flex" justify="center">
      <lz-form
        ref="form"
        :mo1del="form"
        :edit-method="editAdminRole"
        :store-method="storeAdminRole"
        :update-method="updateAdminRole"
        redirect="/admin-roles"
        :form.sync="form"
        :errors.sync="errors"
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
import { editAdminRole, updateAdminRole, storeAdminRole } from '@/api/admin-roles'
import { getAdminPerms } from '@/api/admin-perms'

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
        permissions: [],
      },
      errors: {},
      perms: [],
    }
  },
  created() {
    this.getPerms()
  },
  methods: {
    async editAdminRole(id) {
      const { data } = await editAdminRole(id)
      data.permissions = data.permissions.map(i => i.id)
      return { data }
    },
    updateAdminRole,
    storeAdminRole,
    async getPerms() {
      const { data } = await getAdminPerms()
      this.perms = data.data
    },
    filterMethod(query, item) {
      return item.name.indexOf(query) > -1
    },
  },
}
</script>

<style scoped lang="scss">
/deep/ {
  .el-transfer-panel__body {
    height: 300px;
  }

  .el-transfer-panel__list.is-filterable {
    height: 249px;
  }
}
</style>
