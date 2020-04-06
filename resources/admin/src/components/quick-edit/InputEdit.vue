<template>
  <el-popover
    ref="popover"
    placement="top"
    trigger="click"
    @after-enter="onAfterEnter"
    v-model="editMode"
  >
    <div @keydown.esc="onCancel">
      <el-input
        ref="input"
        v-bind="$attrs"
        v-on="$listeners"
        :disabled="loading"
        size="small"
        @keydown.enter.native="onEnterKeydown"
      />
      <div class="mt-1" style="text-align: right;">
        <el-button-group>
          <el-button
            class="cancel"
            size="mini"
            @click="onCancel"
          >
            取消
          </el-button>
          <loading-action
            ref="confirm"
            :action="onSubmit"
            size="mini"
            type="primary"
          >
            确定
          </loading-action>
        </el-button-group>
      </div>
    </div>
    <span slot="reference" class="value">{{ $attrs.value }}</span>
  </el-popover>
</template>

<script>
import Mixin from './Mixin'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'InputEdit',
  components: {
    LoadingAction,
  },
  mixins: [
    Mixin,
  ],
  data() {
    return {
      editMode: false,
      oldVal: this.$attrs.value,
    }
  },
  methods: {
    onCancel() {
      this.editMode = false
      this.changeVal(this.oldVal)
    },
    onSuccess() {
      this.setOldVal()
      this.editMode = false
    },
    onAfterEnter() {
      this.$refs.input.focus()
    },
    async onEnterKeydown() {
      await this.$refs.confirm.onAction()
      this.editMode = false
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.value {
  padding-bottom: 2px;
  color: $--color-primary;
  border-bottom: dashed 2px $--color-primary;
  cursor: pointer;
  line-height: 26px;
}
</style>
