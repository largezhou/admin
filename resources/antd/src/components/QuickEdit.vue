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
        // 更好的方式拿到 input 元素，很麻烦
        window.setTimeout(() => {
          this.focus()
        }, 100)
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
            v-on:keydown_enter_navite={this.$refs.confirm.onAction}
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
@import "~@/styles/vars";

.value {
  padding-bottom: 2px;
  color: @blue-6;
  border-bottom: 2px dashed @primary-color;
  cursor: pointer;
}
</style>
