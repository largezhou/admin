<script>
import Space from '@c/Space'
import LoadingAction from '@c/LoadingAction'
import { getMessage } from '@/libs/utils'

export default {
  name: 'QuickEdit',
  components: {
    LoadingAction,
    Space,
  },
  data() {
    return {
      editMode: false,
      formValue: this.value,
      loading: false,
    }
  },
  props: {
    value: null,
    update: Function,
    field: String,
    id: [String, Number],
  },
  methods: {
    async onSubmit() {
      try {
        await this
          .update(this.id, { [this.field]: this.formValue })
          .setConfig({ showValidationMsg: true })

        this.$message.success(getMessage('updated'))
      } catch (e) {
        this.$nextTick(() => {
          this.focus()
        })
        throw e
      }

      this.editMode = false
      this.$emit('input', this.formValue)
    },
    focus() {
      return this.$refs.input?.focus()
    },
  },
  watch: {
    editMode(newVal) {
      if (newVal) {
        this.formValue = this.value
        window.setTimeout(() => {
          this.focus()
        }, 300)
      }
    },
  },
  render(h) {
    return (
      <a-popover
        placement="topLeft"
        trigger="click"
        v-model={this.editMode}
      >
        <template slot="content">
          <a-input
            {...{
              attrs: this.$attrs,
              listeners: this.$listeners,
            }}
            ref="input"
            disabled={this.loading}
            v-model={this.formValue}
          />
          <space class="mt-1">
            <a-button
              size="small"
              v-on:click={() => { this.editMode = false }}
            >
              取消
            </a-button>
            <loading-action
              ref="confirm"
              size="small"
              type="primary"
              action={this.onSubmit}
              v-on:action-loading={(v) => { this.loading = v }}
            >
              确定
            </loading-action>
          </space>
        </template>
        <span class="value">{this.value}</span>
      </a-popover>
    )
  },
}
</script>

<style scoped lang="less">
@import "~ant-design-vue/lib/style/color/colors.less";

.value {
  padding-bottom: 2px;
  color: @blue-6;
  border-bottom: dashed 2px @blue-6;
  cursor: pointer;
  line-height: 26px;
}
</style>
