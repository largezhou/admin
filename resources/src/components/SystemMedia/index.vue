<template>
  <el-card>
    <template v-slot:header>选择产品主图</template>
    <el-container class="body">
      <el-aside class="aside" width="221px">
        <el-input
          class="filter-input mb-1"
          placeholder="搜索分类"
          size="small"
          v-model="categoryQ"
        />
        <el-button-group class="category-actions mb-2">
          <loading-action size="mini" :action="getCategories" hide-text>刷新</loading-action>
          <el-button size="mini">添加</el-button>
          <el-button :disabled="!currentCategoryId" size="mini">编辑</el-button>
          <pop-confirm
            :disabled="!currentCategoryId"
            size="mini"
            type="danger"
            :confirm="onDestroyCategory"
          >
            删除
          </pop-confirm>
        </el-button-group>
        <el-scrollbar class="scroll-wrapper">
          <div class="side-tree">
            <el-tree
              v-loading="categoriesLoading"
              class="filter-tree"
              :expand-on-click-node="false"
              :data="categoriesWithAll"
              :props="treeOptions"
              default-expand-all
              :filter-node-method="categoriesFilter"
              ref="tree"
              :indent="8"
              node-key="id"
              current-node-key="1"
              highlight-current
              @current-change="onCurrentChange"
            />
            <div/>
          </div>
        </el-scrollbar>
      </el-aside>
      <el-container>
        <el-header>
          <el-button-group>
            <loading-action :action="onReloadMedia">刷新</loading-action>
            <el-button>上传</el-button>
            <el-button :disabled="!anySelected" @click="movingDialog = true">移动</el-button>
            <pop-confirm type="danger" :disabled="!anySelected" :confirm="onDestroyMedia">删除</pop-confirm>
            <el-switch v-model="multiple"/>
          </el-button-group>
        </el-header>
        <el-main v-loading="mediaLoading">
          <div class="h-100">
            <el-scrollbar class="scroll-wrapper h-100">
              <div class="file-wrapper">
                <div
                  class="file-preview"
                  v-for="(item, i) of media"
                  :class="{ selected: isSelected(item) }"
                  :key="item.id"
                  @click="onSelect(item, i)"
                >
                  <img :src="item.url">
                </div>
              </div>
            </el-scrollbar>
          </div>
        </el-main>
        <el-footer>
          <el-button-group>
            <el-button type="primary" :disabled="!anySelected">选定</el-button>
            <el-button :disabled="!anySelected" @click="clearSelected">清空 {{ countTip }}</el-button>
            <el-button @click="extDialog = true" :title="ext">{{ ext ? '已筛选' : '筛选' }}</el-button>
          </el-button-group>
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

    <el-dialog
      title="筛选类型"
      :visible.sync="extDialog"
      :width="miniWidth ? '90%' : '300px'"
      @keydown.enter.native="onExtFilter"
    >
      <el-input v-model="extTemp" autocomplete="off" placeholder="多个之间用英文逗号隔开"/>
      <div slot="footer" class="dialog-footer">
        <el-button @click="extDialog = false">取消</el-button>
        <el-button type="primary" @click="onExtFilter">确定</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="移动文件"
      :visible.sync="movingDialog"
      :width="miniWidth ? '90%' : '400px'"
      :auto-focus="false"
    >
      <el-select
        v-model="movingTarget"
        filterable
        placeholder="请选择目标分类"
      >
        <el-option
          v-for="i of categoriesSelectOptions"
          :key="i.id"
          :label="i.title"
          :value="i.id"
        >
          <span>{{ i.text }}</span>
        </el-option>
      </el-select>
      <div slot="footer" class="dialog-footer">
        <el-button @click="movingDialog = false">取消</el-button>
        <loading-action
          type="primary"
          :action="onMove"
          :disabled="!movingTarget"
        >
          移动
        </loading-action>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import { batchDestroyMedia, batchUpdateMedia, destroyCategory, getCategories, getCategoryMedia, getMedia } from '@/api/system-media'
import _get from 'lodash/get'
import FlexSpacer from '@c/FlexSpacer'
import Pagination from '@c/Pagination'
import _findIndex from 'lodash/findIndex'
import { getFirstError, getMessage, nestedToSelectOptions, removeFromNested } from '@/libs/utils'
import _differenceBy from 'lodash/differenceBy'

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
      extTemp: '', // 弹框中输入时，未确认的值
      extDialog: false,

      selected: [],
      multiple: false,

      movingDialog: false,
      moving: false,
      movingTarget: '',
    }
  },
  computed: {
    currentCategoryId() {
      return _get(this.currentCategory, 'id', 0)
    },
    miniWidth() {
      return this.$store.state.miniWidth
    },
    anySelected() {
      return this.selectedCount > 0
    },
    selectedCount() {
      return this.selected.length
    },
    countTip() {
      return this.selectedCount
        ? `(${this.selectedCount})`
        : ''
    },
    categoriesSelectOptions() {
      log('select options changed')
      return nestedToSelectOptions(this.categories, {
        title: 'name',
        firstLevel: null,
      })
    },
    categoriesWithAll() {
      return [
        {
          id: 0,
          name: '所有分类',
        },
        ...this.categories,
      ]
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
    onExtFilter() {
      this.ext = this.extTemp
      this.extDialog = false
    },
    onSelect(media, index) {
      const i = this.findInSelected(media)

      if (i !== -1) { // 已经选了，则取消选择
        this.selected.splice(i, 1)
      } else { // 否则加入选择
        if (this.multiple) {
          this.selected.push(media)
        } else {
          this.selected = [media]
        }
      }
    },
    isSelected(media) {
      return this.findInSelected(media) !== -1
    },
    findInSelected(media) {
      return _findIndex(this.selected, (i) => i.id === media.id)
    },
    clearSelected() {
      this.selected = []
    },
    async onMove() {
      if (!this.movingTarget || !this.selectedCount) {
        return
      }
      if (this.movingTarget === this.currentCategoryId) {
        this.$message.info('没有移动到其他分类')
        this.movingDialog = false
        return
      }

      await this.batchUpdateMedia({
        id: this.selected.map((i) => i.id),
        category_id: this.movingTarget,
      })
    },
    async batchUpdateMedia(data) {
      try {
        await batchUpdateMedia(data)
        this.movingDialog = false
        this.$message.success(getMessage('updated'))

        if (this.currentCategoryId) { // 如果是在特定分类下，则要移除图片
          this.moveSelected()
        } else { // 否则只要清除选中
          this.clearSelected()
        }
        this.currentCategoryId && this.moveSelected()
      } catch (e) {
        const msg = getFirstError(e.response)
        msg && this.$message.error(msg)

        // 如果没有 422 错误，说明可能是其他错误，要抛出
        if (!msg) {
          throw e
        }
      }
    },
    moveSelected() {
      // 从列表中，去掉已选择的
      this.media = _differenceBy(this.media, this.selected, 'id')
      this.clearSelected()
    },
    async onDestroyMedia() {
      if (!this.selectedCount) {
        return
      }

      await batchDestroyMedia(this.selected.map((i) => i.id))
      this.$message.success(getMessage('destroyed'))
      this.moveSelected()
    },
    async onDestroyCategory() {
      const id = this.currentCategoryId

      if (!id) {
        return
      }

      await destroyCategory(id)
      this.$message.success(getMessage('destroyed'))
      removeFromNested(this.categories, id)
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
      this.clearSelected()
      this.getMedia(newVal)
    },
    extDialog(newVal) {
      if (newVal) {
        this.extTemp = this.ext
      }
    },
    ext(newVal) {
      this.clearSelected()
      this.getMedia(this.currentCategoryId)
    },
    multiple(newVal) {
      // 切换为单选时，只保留第一个
      if (!newVal) {
        this.selected.splice(1)
      }
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

.filter-input,
.category-actions {
  width: 185px;
}

.category-actions {
  display: flex;

  > .el-button {
    width: 25%;
    flex-grow: 1;
  }

  > .el-button:not(.link) {
    padding: 7px 0;
  }

  > /deep/ .el-button.link {
    a {
      padding: 7px 10px;
    }
  }
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
  cursor: pointer;

  img {
    max-width: 100%;
    max-height: 100%;
  }

  &.selected {
    border-color: $--color-primary;
    border-style: dashed;
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
    height: calc(100% - 60px);

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
