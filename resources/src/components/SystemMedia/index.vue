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
              v-loading="categoriesLoading"
              class="filter-tree"
              :expand-on-click-node="false"
              :data="categories"
              :props="treeOptions"
              default-expand-all
              :filter-node-method="categoriesFilter"
              ref="tree"
              :indent="8"
              node-key="id"
              current-node-key="1"
              highlight-current
              @current-change="onCurrentChange"
            >
              <template v-slot="{ node, data }">
                <div class="tree-node">
                  <el-button-group v-if="data.id" class="actions">
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
          <loading-action :action="getCategories" :loading="categoriesLoading">刷新分类</loading-action>
          <loading-action :action="onReloadMedia" :loading="mediaLoading">刷新文件</loading-action>
        </el-header>
        <el-main v-loading="mediaLoading">
          <div class="h-100">
            <el-scrollbar class="scroll-wrapper h-100">
              <div class="file-wrapper">
                <div
                  class="file-preview"
                  v-for="(item, i) of media"
                  :key="item.id"
                >
                  <img :src="item.url">
                </div>
              </div>
            </el-scrollbar>
          </div>
        </el-main>
        <el-footer>
          <el-button type="primary">上传</el-button>
          <el-button type="primary">选定</el-button>
          <el-button>筛选</el-button>
          <flex-spacer/>
          <pagination
            :page="page"
            layout="total, prev, pager, next"
            :auto-push="false"
            @current-change="onPageChange"
          />
        </el-footer>
      </el-container>
    </el-container>
  </el-card>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import { getCategories, getCategoryMedia, getMedia } from '@/api/system-media'
import _get from 'lodash/get'
import FlexSpacer from '@c/FlexSpacer'
import Pagination from '@c/Pagination'

export default {
  name: 'SystemMedia',
  components: {
    Pagination,
    FlexSpacer,
    PopConfirm,
  },
  data() {
    return {
      treeOptions: {
        children: 'children',
        label: 'name',
      },

      categoryQ: '',
      categories: [],
      categoriesLoading: false,
      currentCategory: null,

      media: [],
      mediaLoading: false,
      page: null,
      ext: '',
    }
  },
  computed: {
    currentCategoryId() {
      return _get(this.currentCategory, 'id', 0)
    },
  },
  async created() {
    await this.getCategories()
    await this.getMedia()
  },
  methods: {
    async getCategories() {
      this.categoriesLoading = true
      try {
        const { data } = await getCategories()
        data.unshift({
          id: 0,
          name: '所有分类',
        })
        this.categories = data
        await this.$nextTick()
        this.$refs.tree.setCurrentKey(0)
        this.currentCategory = null
      } finally {
        this.categoriesLoading = false
      }
    },
    categoriesFilter(value, category) {
      if (!value) {
        return true
      }
      return category.name.indexOf(value) !== -1
    },
    async getMedia(categoryId = 0, page) {
      this.mediaLoading = true
      let data
      const params = {
        page,
        ext: this.ext || undefined,
      }
      try {
        if (categoryId) {
          ({ data } = await getCategoryMedia(categoryId, params))
        } else {
          ({ data } = await getMedia(params))
        }

        // 如果在请求图片时，且换了分类，则不做处理
        if (this.currentCategoryId !== categoryId) {
          return
        }

        this.media = data.data
        this.page = data.meta
      } finally {
        this.mediaLoading = false
      }
    },
    onCurrentChange(category, node) {
      this.currentCategory = category
      log(arguments)
    },
    onPageChange(page) {
      this.getMedia(this.currentCategoryId, page)
    },
    async onReloadMedia() {
      await this.getMedia(this.currentCategoryId)
    },
    test() {
      log(...arguments)
    },
  },
  watch: {
    categoryQ(val) {
      this.$refs.tree.filter(val)
    },
    currentCategoryId(newVal) {
      this.getMedia(newVal)
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
  height: 550px;
}

.side-tree {
  min-width: calc(100% - 15px);
  padding: 0 15px 15px 0;
  display: inline-block;
}

.filter-input {
  width: 185px;
}

.file-wrapper {
  display: flex;
  flex-wrap: wrap;
}

.file-preview {
  width: 100px;
  height: 100px;
  display: inline-flex;
  align-items: center;
  border: 3px solid $--color-info-light;
  margin-right: 5px;
  margin-bottom: 5px;
  justify-content: center;

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

  .el-tree-node__content {
    height: 30px;
  }
}
</style>
