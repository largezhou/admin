<template>
  <div
    v-click-outside="onCancel"
    @keydown.esc="onCancel"
    class="input-edit"
  >
    <span
      class="value"
      @click="editMode = true"
      v-show="!editMode"
    >
      {{ $attrs.value }}
      <i v-show="!$attrs.value" class="el-icon-edit"/>
    </span>

    <el-input
      v-show="editMode"
      ref="input"
      v-bind="$attrs"
      v-on="$listeners"
      :disabled="loading"
      size="small"
      @keydown.enter.native="onSubmit"
    />
    <el-button-group v-show="editMode">
      <el-button
        class="cancel"
        size="mini"
        icon="el-icon-close"
        @click="onCancel"
      />
      <el-button
        :loading="loading"
        :disabled="loading"
        @click="onSubmit"
        size="mini"
        type="primary"
        icon="el-icon-check"
      />
    </el-button-group>
  </div>
</template>

<script>
import Mixin from './Mixin'

export default {
  name: 'InputEdit',
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
    },
    onSuccess() {
      this.setOldVal()
      this.onCancel()
    },
  },
  watch: {
    editMode(newVal) {
      if (!newVal) {
        this.changeVal(this.oldVal)
      } else {
        this.$nextTick(() => {
          this.focus()
        })
      }
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.input-edit {
  display: flex;
  align-items: center;
}

.value {
  color: $--color-primary;
  border-bottom: dashed 2px $--color-primary;
  cursor: pointer;
}

::v-deep {
  .el-input {
    width: calc(100% - 62px);
    max-width: 300px;
  }

  .el-input__inner {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    padding: 0 10px;
  }

  .el-button {
    padding: 9px;
    height: 32px;
  }
}

.cancel {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  border-left: none;
}
</style>
