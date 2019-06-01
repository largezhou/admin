<template>
  <el-form :model="form" @keydown.enter.native="onSubmit">
    <el-form-item :error="errors.username">
      <el-input v-model="form.username" placeholder="账号">
        <i slot="prepend" class="el-icon-user"/>
      </el-input>
    </el-form-item>
    <el-form-item :error="errors.password">
      <el-input v-model="form.password" placeholder="密码" type="password">
        <i slot="prepend" class="el-icon-lock"/>
      </el-input>
    </el-form-item>
    <el-form-item>
      <el-button
        class="login-btn"
        type="primary"
        @click="onSubmit"
      >
        登录
      </el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { handleValidateErrors } from '@/libs/utils'

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
        this.$message.success('登录成功')
        this.$router.push(this.$route.query.redirect || { name: 'index' })
      } catch (e) {
        this.errors = handleValidateErrors(e)
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
