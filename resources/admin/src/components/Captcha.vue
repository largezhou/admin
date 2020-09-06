<script>
import { getCaptchaConfig } from '@/api/configs'

export default {
  name: 'Captcha',
  data: () => ({
    captcha: null,
    loading: false,
  }),
  props: {
    config: String,
    altInfo: {
      type: String,
      default: '点击刷新',
    },
  },
  created() {
    this.getCaptchaConfig()
  },
  methods: {
    async reload() {
      await this.getCaptchaConfig()
    },
    async getCaptchaConfig() {
      this.loading = true
      try {
        const { data } = await getCaptchaConfig(this.config)
        this.captcha = data
      } finally {
        this.loading = false
      }
    },
  },
  watch: {
    captcha(newVal) {
      this.$emit('captcha', newVal)
    },
  },
  render(h) {
    return (
      <div
        v-on:click={this.getCaptchaConfig}
        class="captcha-wrapper"
        title={this.altInfo}
      >
        <a-icon v-show={this.loading} type="loading"/>
        <img
          v-show={!this.loading}
          src={this.captcha?.img}
          alt={this.altInfo}
        />
      </div>
    )
  },
}
</script>

<style scoped lang="less">
.captcha-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  cursor: pointer;

  i {
    font-size: 16px;
  }
}
</style>
