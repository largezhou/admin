<template>
  <Form
    ref="loginForm"
    :model="form"
    @keydown.enter.native="onSubmit"
  >
    <FormItem prop="username" :error="formErrors.username">
      <Input v-model="form.username" placeholder="请输入用户名">
        <span slot="prepend">
          <Icon :size="16" type="ios-person"/>
        </span>
      </Input>
    </FormItem>
    <FormItem prop="password" :error="formErrors.password">
      <Input type="password" v-model="form.password" placeholder="请输入密码">
        <span slot="prepend">
          <Icon :size="14" type="md-lock"/>
        </span>
      </Input>
    </FormItem>
    <FormItem>
      <Button @click="onSubmit" type="primary" long>登录</Button>
    </FormItem>
  </Form>
</template>

<script>
import utils from '@/libs/utils'

export default {
  name: 'LoginForm',
  data: () => ({
    form: {
      username: '',
      password: '',
    },
    formErrors: {},
  }),
  methods: {
    async onSubmit() {
      this.formErrors = {}
      try {
        await this.$store.dispatch('login', this.form)
        this.$Message.success('登录成功')
        this.$router.push(this.$route.query.redirect || { name: 'index' })
      } catch (e) {
        this.formErrors = utils.handleValidateErrors(e)
      }
    },
  },
}
</script>

<style scoped lang="scss">
/deep/ {
  .ivu-input-group-prepend {
    width: 32px;
  }
}
</style>
