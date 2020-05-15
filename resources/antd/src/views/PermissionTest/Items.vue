<template>
  <div>
    <a-tag
      :key="tag"
      v-for="tag in items"
      closable
      :disable-transitions="false"
      @close="handleClose(tag)"
    >
      {{ tag }}
    </a-tag>
    <a-input
      class="input-new-tag"
      v-show="inputVisible"
      v-model="inputValue"
      ref="saveTagInput"
      @keyup.enter.native="handleInputConfirm"
      @blur="handleInputConfirm"
      size="small"
    />
    <a-button
      v-show="!inputVisible"
      class="button-new-tag ml-1"
      @click="showInput"
      size="small"
    >
      + New Tag
    </a-button>
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
      this.inputValue && this.items.push(this.inputValue)
      this.inputVisible = false
      this.inputValue = ''
    },
  },
}
</script>

<style scoped>
.input-new-tag {
  width: 90px;
  margin-left: 10px;
}
</style>
