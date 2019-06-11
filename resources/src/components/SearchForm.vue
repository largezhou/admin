<template>
  <el-collapse class="search-toggle" v-model="show">
    <el-collapse-item name="only">
      <template v-slot:title>
        <i class="el-icon-search"/>
        <span style="margin-left: 5px;">筛选</span>
      </template>
      <el-form inline @keydown.enter.native="onSubmit">
        <el-form-item v-for="item of fields" :key="item.field">
          <component
            :is="item.type || 'el-input'"
            v-model="form[item.field]"
            :placeholder="item.label"
            clearable
          />
        </el-form-item>
        <el-form-item style="display: block">
          <el-button type="primary" @click="onSubmit">查询</el-button>
          <el-button @click="onReset">重置</el-button>
        </el-form-item>
      </el-form>
    </el-collapse-item>
  </el-collapse>
</template>

<script>
export default {
  name: 'SearchForm',
  data() {
    return {
      form: {},
      show: [],
    }
  },
  props: {
    fields: Array,
  },
  created() {
    this.initFormShow()
  },
  methods: {
    onSubmit() {
      const query = { ...this.$route.query }

      // 构建查询对象, 空值的不放入, 保留非搜索表单的 query 字段
      this.fields.forEach(i => {
        const key = i.field
        let val = this.form[key]
        val = (val === undefined) ? '' : val.trim()
        if (val === '') {
          delete query[key]
        } else {
          query[key] = val
        }
      })

      this.$router.push({
        path: this.$route.path,
        query,
      })
    },
    onReset() {
      this.form = {}
      this.onSubmit()
    },
    initFormShow() {
      this.fields.some(i => {
        if (this.$route.query[i.field]) {
          this.show = ['only']
          return true
        }
      })
    },
  },
  watch: {
    $route: {
      handler(newVal) {
        const { query } = newVal
        this.fields.forEach(i => {
          const key = i.field
          const val = query[key]
          this.$set(this.form, key, val)
        })
      },
      immediate: true,
    },
  },
}
</script>

<style scoped lang="scss">
.search-toggle /deep/ {
  border: none;
  margin-top: -20px;

  .el-collapse-item__header {
    /*display: block;*/
    /*text-align: center;*/
    color: #409eff;
    font-size: 16px;
    border: none;
  }

  .el-collapse-item__arrow {
    display: none;
  }
}
</style>
