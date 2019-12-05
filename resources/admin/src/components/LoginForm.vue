<template>
  <el-form :model="form" label-width="0">
    <el-form-item :error="errors.username">
      <el-input v-model="form.username" placeholder="账号" autofocus>
        <svg-icon slot="prepend" icon-class="user"/>
      </el-input>
    </el-form-item>
    <el-form-item :error="errors.password">
      <el-input v-model="form.password" placeholder="密码" type="password">
        <svg-icon slot="prepend" icon-class="password"/>
      </el-input>
    </el-form-item>
  </el-form>
</template>

<script>
import { getMessage } from '@/libs/utils'

export default {
  name: 'LoginForm',
  data: () => ({
    form: {
      username: '',
      password: '',
    },
    errors: {},
  }),
  methods: {
    async onSubmit() {
      await this.$store.dispatch('login', this)
      this.$message.success(getMessage('loggedIn'))
    },
  },
}
</script>

<style scoped lang="scss">
::v-deep {
  .el-input-group__prepend {
    padding: 0 12px;
  }
}
</style>
