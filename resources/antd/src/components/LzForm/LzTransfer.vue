<script>
import _diffBy from 'lodash/differenceBy'

export default {
  name: 'LzTransfer',
  props: {
    value: Array,
    titles: {
      type: Array,
      default: () => ['待选', '已选'],
    },
    listStyle: {
      type: Object,
      default: () => ({
        height: '300px',
      }),
    },
  },
  methods: {
    filterOption(val, option) {
      return option.title.toLowerCase().indexOf(val.trim().toLowerCase()) !== -1
    },
    onChange(tar, dir, move) {
      this.$emit('change', ...arguments)
      this.$emit('input', dir === 'right' ? tar : _diffBy(this.value, move, String))
    },
  },
  render(h) {
    return (
      <a-transfer
        {...{
          attrs: Object.assign({}, this.$attrs, this.$props),
          listeners: this.$listeners,
        }}
        target-keys={this.value.map(String)}
        render={i => i.title}
        filter-option={this.filterOption}
        show-search={true}
        v-on:change={this.onChange}
        scopedSlots={this.$scopedSlots}
      >{this.$slots}</a-transfer>
    )
  },
}
</script>

<style scoped lang="less">
.ant-transfer {
  display: flex;
  align-items: center;

  ::v-deep {
    .ant-transfer-list {
      flex-grow: 1;
    }
  }
}

@media (max-width: 768px) {
  .ant-transfer {
    flex-direction: column;

    ::v-deep {
      .ant-transfer-list {
        width: 100%;
      }

      .ant-transfer-operation {
        display: flex;
        align-items: center;
        margin: 8px;

        > button {
          transform: rotate(90deg);
          margin: 0;

          + button {
            margin-left: 8px;
          }
        }
      }
    }
  }
}
</style>
