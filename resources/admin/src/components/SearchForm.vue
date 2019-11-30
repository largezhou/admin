<script>
export default {
  name: 'SearchForm',
  data() {
    return {
      form: {},
      show: false,
    }
  },
  computed: {
    anyQuery() {
      return this.fields.some(i => {
        if (this.$route.query[i.field]) {
          return true
        }
      })
    },
  },
  props: {
    fields: Array,
    /**
     * 筛选后，重置当前页到第一页
     */
    resetCurrentPage: {
      type: Boolean,
      default: true,
    },
  },
  created() {
    this.initFormShow()
  },
  methods: {
    async onSubmit() {
      const query = { ...this.$route.query }
      if (this.resetCurrentPage) {
        delete query.page
      }

      // 构建查询对象, 空值的不放入, 保留非搜索表单的 query 字段
      this.fields.forEach(i => {
        const key = i.field
        let val = this.form[key]
        if (typeof val === 'string') {
          val = val.trim()
        }
        if (val === '' || val === undefined) {
          delete query[key]
        } else {
          query[key] = val
        }
      })

      try {
        await this.$router.push({
          path: this.$route.path,
          query,
        })
      } catch (e) {
        if (e.name !== 'NavigationDuplicated') {
          throw e
        }
      }
    },
    onReset() {
      this.form = {}
      this.onSubmit()
    },
    initFormShow() {
      this.fields.some(i => {
        if (this.$route.query[i.field]) {
          this.show = true
          return true
        }
      })
    },
    setFormValueFromQuery() {
      const query = this.$route.query
      this.fields.forEach(i => {
        const key = i.field
        const val = query[key]
        this.$set(this.form, key, val)
      })
    },
  },
  watch: {
    $route: {
      handler() {
        this.setFormValueFromQuery()
      },
      immediate: true,
    },
  },

  render(h) {
    return (
      <el-button type={this.anyQuery ? 'primary' : ''} class="search-button">
        <el-popover
          placement="bottom-start"
          trigger="click"
          popper-class="search-form-popover"
        >
          <el-form
            class="pb-3"
            inline
            vOn:keydown_enter_native_prevent={this.onSubmit}
          >
            {
              this.fields.map((item) => {
                let c
                switch (item.type) {
                  case 'el-select':
                    c = (
                      <el-select
                        v-model={this.form[item.field]}
                        placeholder={item.label}
                        filterable
                        clearable
                      >
                        {
                          item.options.map((i) => {
                            const valueField = item.valueField || 'id'
                            const labelField = item.labelField || 'name'
                            return (
                              <el-option
                                key={i[valueField]}
                                label={i[labelField]}
                                value={String(i[valueField])}
                              />
                            )
                          })
                        }
                      </el-select>
                    )
                    break
                  default: // 默认是 input
                    c = (
                      <el-input
                        v-model={this.form[item.field]}
                        placeholder={item.label}
                        clearable
                      />
                    )
                }

                return (
                  <el-form-item key={item.field}>{c}</el-form-item>
                )
              })
            }

            <el-form-item class="actions">
              <el-button type="primary" vOn:click={this.onSubmit}>查询</el-button>
              <el-button vOn:click={this.onReset}>重置</el-button>
            </el-form-item>
          </el-form>
          <span class="trigger" slot="reference"/>
        </el-popover>
        筛选
      </el-button>
    )
  },
}
</script>

<style scoped lang="scss">
.search-button {
  position: relative;
}

.actions {
  display: block;
  margin-bottom: 0;
}

.trigger {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
}
</style>

<style>
.search-form-popover {
  max-width: 90%;
}
</style>
