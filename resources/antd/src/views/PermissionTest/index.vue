<template>
  <page-content>
    <div class="mb-3">
      <p>角色</p>
      <items :items="roles"/>
      <!--每次更新，都重新渲染该部分，可以重置指令-->
      <div class="mt-3" :key="Date.now()">
        <a-tag v-role-in="needRoles">有权限（{{ needRoles.join(',') }},administrator 有一个）</a-tag>
        <a-tag v-role-in.not="needRoles">无权限（{{ needRoles.join(',') }},administrator 都没有）</a-tag>
      </div>
    </div>
    <div>
      <p>权限</p>
      <items :items="perms"/>
      <!--每次更新，都重新渲染该部分，可以重置指令-->
      <div class="mt-3" :key="Date.now()">
        <a-tag v-can="needPerms">有权限（{{ needPerms.join(',') }} 都有, 或者有 pass-all）</a-tag>
        <a-tag v-can.not="needPerms">无权限（{{ needPerms.join(',') }} 有一个没有，且没有 pass-all）</a-tag>
      </div>
    </div>
  </page-content>
</template>

<script>
import Items from '@v/PermissionTest/Items'
import PageContent from '@c/PageContent'

export default {
  name: 'PermissionTest',
  components: {
    PageContent,
    Items,
  },
  data() {
    return {
      needRoles: ['editor', 'manager'],
      needPerms: ['edit-post', 'check-post'],
    }
  },
  computed: {
    roles() {
      return this.$store.getters.userInfo('roles', [])
    },
    perms() {
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
