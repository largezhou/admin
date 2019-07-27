<template>
  <el-form :model="form" @keydown.enter.native="$refs.submit.onAction" label-width="0">
    <el-form-item :error="errors.username">
      <el-input v-model="form.username" placeholder="账号">
        <svg-icon slot="prepend" icon-class="user"/>
      </el-input>
    </el-form-item>
    <el-form-item :error="errors.password">
      <el-input v-model="form.password" placeholder="密码" type="password">
        <svg-icon slot="prepend" icon-class="password"/>
      </el-input>
    </el-form-item>
    <el-form-item>
      <loading-action
        ref="submit"
        class="login-btn"
        type="primary"
        :action="onSubmit"
      >
        登录
      </loading-action>
    </el-form-item>
    <el-form-item>
      <loading-action
        class="login-btn"
        type="danger"
        plain
        :action="onResetSystem"
      >
        重置系统
      </loading-action>
    </el-form-item>
  </el-form>
</template>

<script>
import { getMessage, handleValidateErrors } from '@/libs/utils'
import axios from '@/plugins/axios'

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
      this.errors = {}
      try {
        await this.$store.dispatch('login', this.form)
        this.$message.success(getMessage('loggedIn'))
        this.$router.push(this.$route.query.redirect || '/')
      } catch (e) {
        this.errors = handleValidateErrors(e.response)
      }
    },
    async onResetSystem() {
      if (confirm('确认重置？')) {
        await axios.post('/demo/reset-system')
        this.$message.success('已重置，admin 密码为 000000')
      }
    },
  },
}
</script>

<style scoped lang="scss">
.login-btn {
  width: 100%;
}

/deep/ {
  .el-input-group__prepend {
    padding: 0 12px;
  }
}
</style>
