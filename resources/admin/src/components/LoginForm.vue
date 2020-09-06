<template>
  <lz-form
    :form.sync="form"
    :errors.sync="errors"
    in-dialog
    :footer="false"
  >
    <lz-form-item prop="username">
      <a-input
        ref="username"
        v-model="form.username"
        placeholder="帐号"
      >
        <svg-icon slot="prefix" icon-class="user"/>
      </a-input>
    </lz-form-item>
    <lz-form-item prop="password">
      <a-input
        type="password"
        v-model="form.password"
        placeholder="密码"
      >
        <svg-icon slot="prefix" icon-class="password"/>
      </a-input>
    </lz-form-item>
    <lz-form-item v-if="loginCaptcha" prop="captcha">
      <space>
        <a-input
          v-model="form.captcha"
          placeholder="验证码"
        >
          <svg-icon slot="prefix" icon-class="captcha"/>
        </a-input>
        <div class="captcha">
          <captcha ref="captcha" @captcha="getCaptchaConfig" config="admin"/>
        </div>
      </space>
    </lz-form-item>
  </lz-form>
</template>

<script>
import { getMessage } from '@/libs/utils'
import LzForm from '@c/LzForm/index'
import LzFormItem from '@c/LzForm/LzFormItem'
import Space from '@c/Space'
import Captcha from '@c/Captcha'
import { SYSTEM_BASIC } from '@/libs/constants'

export default {
  name: 'LoginForm',
  components: {
    Captcha,
    Space,
    LzFormItem,
    LzForm,
  },
  data: () => ({
    form: {
      username: '',
      password: '',
      captcha: '',
      key: '',
    },
    errors: {},
  }),
  computed: {
    loginCaptcha() {
      const v = this.$store.getters.getConfig(
        SYSTEM_BASIC.SLUG + '.' + SYSTEM_BASIC.ADMIN_LOGIN_CAPTCHA_SLUG,
        SYSTEM_BASIC.DEFAULT_ADMIN_LOGIN_CAPTCHA,
      )

      return Boolean(Number(v))
    },
  },
  mounted() {
    this.$refs.username.focus()
  },
  methods: {
    async onSubmit() {
      try {
        await this.$store.dispatch('login', this)
        this.$message.success(getMessage('loggedIn'))
      } catch (e) {
        this.$refs.captcha.reload()
        throw e
      }
    },
    async getCaptchaConfig(captcha) {
      this.form.key = captcha.key
    },
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/vars";

.captcha {
  width: 100px;
  height: 32px;

  ::v-deep img {
    border-radius: @border-radius-base;
  }
}
</style>
