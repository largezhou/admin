<template>
  <el-card>
    <template v-slot:header>
      <content-header/>
    </template>
    <div class="mb-3">
      <p>角色</p>
      <items :items="roles"/>
    </div>
    <div>
      <p>权限</p>
      <items :items="perms"/>
    </div>
    <div class="test">
      <p>权限角色测试</p>
      <el-tag v-role-in="roleIn">administrator 或者有角色之一，可显示</el-tag>
      <el-input v-model="roleIn"/>
      <el-tag v-can="can">pass-all 或者权限都有，可显示</el-tag>
      <el-input v-model="can"/>
    </div>
  </el-card>
</template>

<script>
import Items from '@v/PermissionTest/Items'
import _get from 'lodash/get'

export default {
  name: 'PermissionTest',
  components: {
    Items,
  },
  data() {
    return {
      roleIn: 'editor,manager',
      can: 'edit-post,check-post',
    }
  },
  computed: {
    roles() {
      // return _get(this.$store, 'state.users.user.roles', [])
      return this.$store.getters.userInfo('roles', [])
    },
    perms() {
      // return _get(this.$store, 'state.users.user.permissions', [])
      return this.$store.getters.userInfo('permissions', [])
    },
  },
}
</script>

<style scoped>
.el-tag + .el-tag {
  margin-left: 10px;
}

.button-new-tag {
  margin-left: 10px;
  height: 32px;
  line-height: 30px;
  padding-top: 0;
  padding-bottom: 0;
}

.input-new-tag {
  width: 90px;
  margin-left: 10px;
  vertical-align: bottom;
}

.test > * {
  margin-bottom: 10px;
}
</style>
