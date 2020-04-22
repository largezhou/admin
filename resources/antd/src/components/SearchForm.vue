<script>
import Space from '@c/Space'

export default {
  name: 'SearchForm',
  components: { Space },
  data() {
    return {
      form: {},
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
    maxWidth: {
      type: String,
      default: '680px',
    },
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
      <a-popover
        trigger="click"
        placement="bottomLeft"
        overlay-style={{ padding: '10px', maxWidth: this.maxWidth }}
      >
        <template slot="content">
          <a-form
            layout="inline"
            vOn:keydown_enter_native_prevent={this.onSubmit}
          >
            {
              this.fields.map((item) => {
                let c
                switch (item.type) {
                  case 'select':
                    c = (
                      <a-select
                        style="width: 202.5px;"
                        default-valut={this.form[item.field]}
                        v-model={this.form[item.field]}
                        placeholder={item.label}
                        allow-clear
                        show-search
                      >
                        {
                          item.options.map((i) => {
                            const value = String(i[item.valueField || 'id'])
                            const text = i[item.labelField || 'name']
                            return (<a-select-option value={value}>{text}</a-select-option>)
                          })
                        }
                      </a-select>
                    )
                    break
                  case 'input':
                  default: // 默认是 input
                    c = (
                      <a-input
                        v-model={this.form[item.field]}
                        placeholder={item.label}
                        allow-clear
                      />
                    )
                }

                return (
                  <a-form-item key={item.field}>{c}</a-form-item>
                )
              })
            }
            <a-form-item class="actions">
              <space>
                <a-button type="primary" vOn:click={this.onSubmit}>查询</a-button>
                <a-button vOn:click={this.onReset}>重置</a-button>
              </space>
            </a-form-item>
          </a-form>
        </template>
        <a-button type={this.anyQuery ? 'primary' : ''}>搜索</a-button>
      </a-popover>
    )
  },
}
</script>

<style scoped lang="less">
.actions {
  display: block;
  margin-bottom: 0;
}
</style>
