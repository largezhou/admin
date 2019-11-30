<template>
  <div>
    <el-tag
      :key="tag"
      v-for="tag in items"
      closable
      :disable-transitions="false"
      @close="handleClose(tag)"
      size="normal"
    >
      {{ tag }}
    </el-tag>
    <el-input
      class="input-new-tag"
      v-show="inputVisible"
      v-model="inputValue"
      ref="saveTagInput"
      size="small"
      @keyup.enter.native="handleInputConfirm"
      @blur="handleInputConfirm"
    />
    <el-button
      v-show="!inputVisible"
      class="button-new-tag"
      size="small"
      @click="showInput"
    >
      + New Tag
    </el-button>
  </div>
</template>

<script>
export default {
  name: 'Items',
  data() {
    return {
      inputVisible: false,
      inputValue: '',
    }
  },
  props: {
    items: Array,
  },
  methods: {
    handleClose(tag) {
      this.items.splice(this.items.indexOf(tag), 1)
    },

    showInput() {
      this.inputVisible = true
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus()
      })
    },

    handleInputConfirm() {
      let inputValue = this.inputValue
      if (inputValue) {
        this.items.push(inputValue)
      }
      this.inputVisible = false
      this.inputValue = ''
    },
  },
}
</script>

<style scoped>
.el-tag + .el-tag {
  margin-left: 10px;
}

.button-new-tag {
  margin-left: 10px;
  height: 32px;
  line-height: 30px;
  padding-top: 0;
  padding-bottom: 0;
}

.input-new-tag {
  width: 90px;
  margin-left: 10px;
  vertical-align: bottom;
}
</style>
