<template>
  <el-card>
    <template v-slot:header>选择产品主图</template>
    <el-container class="body">
      <el-aside class="aside" width="221px">
        <el-input
          class="filter-input mb-2"
          placeholder="搜索分类"
          size="small"
          v-model="categoryQ"
        />
        <el-scrollbar class="scroll-wrapper">
          <div class="side-tree">
            <el-tree
              class="filter-tree"
              :expand-on-click-node="false"
              :data="categories"
              :props="treeOptions"
              default-expand-all
              :filter-node-method="categoriesFilter"
              ref="tree"
              :indent="8"
            >
              <template v-slot="{ node, data }">
                <div class="tree-node">
                  <el-button-group class="actions">
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-edit"
                    />
                    <pop-confirm
                      class="delete"
                      type="text"
                      size="mini"
                    >
                      <i class="el-icon-delete"/>
                    </pop-confirm>
                  </el-button-group>
                  <span class="label">{{ node.label }}</span>
                </div>
              </template>
            </el-tree>
            <div/>
          </div>
        </el-scrollbar>
      </el-aside>
      <el-container>
        <el-header>
          <el-button type="primary">移动文件</el-button>
        </el-header>
        <el-main>
          <div class="h-100">
            <el-scrollbar class="scroll-wrapper">
              <div
                class="grid-preview"
                v-for="i of 20"
                :key="i"
              >
                <img :src="url">
              </div>
            </el-scrollbar>
          </div>
        </el-main>
        <el-footer>
          <el-button type="primary">上传</el-button>
          <el-button type="primary">选定</el-button>
          <el-button>筛选</el-button>
        </el-footer>
      </el-container>
    </el-container>
  </el-card>
</template>

<script>
import PopConfirm from '@c/PopConfirm'

export default {
  name: 'SystemMedia',
  components: {
    PopConfirm,
  },
  data() {
    return {
      categoryQ: '',
      categories: [
        {
          id: 1,
          name: '一级 1',
          children: [
            {
              id: 4,
              name: '二级 1-1',
              children: [
                {
                  id: 9,
                  name: '三级 1-1-1',
                }, {
                  id: 10,
                  name: '三级 1-1-2',
                },
              ],
            },
          ],
        },
        {
          id: 2,
          name: '一级 2',
          children: [
            {
              id: 5,
              name: '二级 2-1',
            }, {
              id: 6,
              name: '二级 2-2',
            },
          ],
        },
        {
          id: 3,
          name: '一级 3',
          children: [
            {
              id: 7,
              name: '二级 3-1',
            }, {
              id: 8,
              name: '二级 3-2',
            },
          ],
        },
      ],
      treeOptions: {
        children: 'children',
        label: 'name',
      },

      url: 'https://fuss10.elemecdn.com/e/5d/4a731a90594a4af544c0c25941171jpeg.jpeg',
    }
  },
  methods: {
    categoriesFilter(value, category) {
      if (!value) {
        return true
      }
      return category.name.indexOf(value) !== -1
    },
  },
  watch: {
    categoryQ(val) {
      this.$refs.tree.filter(val)
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

$border: 1px solid $--color-info-light;
$padding-width: 15px;

.aside {
  border-right: $border;
  padding: $padding-width 0 $padding-width 20px;
  overflow: hidden;
}

.body {
  height: 600px;
}

.side-tree {
  min-width: calc(100% - 15px);
  padding: 0 15px 15px 0;
  display: inline-block;
}

.filter-input {
  width: 185px;
}

.grid-preview {
  width: 100px;
  height: 100px;
  display: inline-flex;
  align-items: center;
  border: 3px solid $--color-info-light;
  margin-right: 5px;
  margin-bottom: 5px;

  img {
    max-width: 100%;
    max-height: 100%;
  }
}

.el-header,
.el-footer {
  display: flex;
  align-items: center;
  padding-top: $padding-width;
  padding-bottom: $padding-width;
  height: auto !important;
}

.el-header {
  border-bottom: $border;
}

.el-footer {
  border-top: $border;
}

.tree-node {
  height: 30px;
  display: flex;
  align-items: center;

  .label {
    font-size: 14px;
  }

  .actions {
    width: 45px;

    > .el-button {
      padding-left: 4px;
      padding-right: 4px;
    }

    .delete {
      color: $--color-danger;
    }
  }
}

/deep/ {
  .scroll-wrapper {
    height: calc(100% - 42px);

    .el-scrollbar__wrap {
      height: calc(100% + 17px);
    }
  }

  .el-card__body {
    padding: 0;
  }
}
</style>
