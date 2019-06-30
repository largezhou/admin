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
          <loading-action size="mini" :action="getCategories">刷新</loading-action>
          <el-button
            :disabled="currentCategoryId === 0"
            size="mini"
            @click="onOpenCategoryDialog(false)"
          >
            添加
          </el-button>
          <el-button
            :disabled="currentCategoryId <= 0"
            size="mini"
            @click="onOpenCategoryDialog(true)"
          >
            编辑
          </el-button>
          <pop-confirm
            :disabled="currentCategoryId <= 0"
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
              draggable
              :allow-drag="allowDrag"
              :allow-drop="allowDrop"
              @node-drop="onNodeDrop"
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
            layout="total, pager"
            :auto-push="false"
            @current-change="onPageChange"
            :pager-count="5"
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

    <el-dialog
      :title="categoryEdit ? '编辑分类' : '添加分类'"
      :visible.sync="categoryDialog"
      :width="miniWidth ? '90%' : '300px'"
      @keydown.enter.native="$refs.categorySaveConfirm.onAction"
    >
      <el-input v-model="categoryName" autocomplete="off" placeholder="请输入分类名称"/>
      <div slot="footer" class="dialog-footer">
        <el-button @click="categoryDialog = false">取消</el-button>
        <loading-action ref="categorySaveConfirm" type="primary" :action="onSaveCategory">确定</loading-action>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import { batchDestroyMedia, batchUpdateMedia, destroyCategory, getCategories, getCategoryMedia, getMedia, storeCategory, updateCategory } from '@/api/system-media'
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

      categoryDialog: false,
      categoryName: '',
      categoryEdit: true,
      categoryParentId: 0,

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
      return _get(this.currentCategory, 'id', -1)
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
      return nestedToSelectOptions(this.categories, {
        title: 'name',
        firstLevel: null,
      })
    },
    categoriesWithAll() {
      return [
        {
          id: -1,
          name: '所有',
        },
        {
          id: 0,
          name: '无分类',
        },
        ...this.categories,
      ]
    },
    canSaveCategory() {
      const id = this.currentCategoryId
      // 所有 和 无分类 不能编辑
      if (this.categoryEdit && id <= 0) {
        return false
      }
      // 无分类 不能添加子分类
      if (!this.categoryEdit && id === 0) {
        return false
      }

      return true
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
        this.$refs.tree.setCurrentKey(-1)
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
    async getMedia(categoryId = -1, page) {
      this.mediaLoading = true
      let data
      const params = {
        page,
        ext: this.ext || undefined,
        per_page: 2,
      }
      try {
        if (categoryId > 0) {
          ({ data } = await getCategoryMedia(categoryId, params))
        } else {
          // -1 为获取所有文件，所以不需要传 category_id 参数，设为 undefined 就行
          params.category_id = (categoryId === -1) ? undefined : 0;
          ({ data } = await getMedia(params))
        }

        // 如果在请求图片时，切换了分类，则不做处理
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
    /**
     * 清除选中状态
     */
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

        if (this.currentCategoryId === -1) { // 如果是在所有分类下，则只需要清除选中
          this.clearSelected()
        } else { // 否则，要从数据中清除
          this.moveSelected()
        }
      } catch (e) {
        const msg = getFirstError(e.response)
        msg && this.$message.error(msg)

        // 如果没有 422 错误，说明可能是其他错误，要抛出
        if (!msg) {
          throw e
        }
      }
    },
    /**
     * 从当前数据中，移除选中的文件
     */
    moveSelected() {
      // 从列表中，去掉已选择的
      this.media = _differenceBy(this.media, this.selected, 'id')
      this.clearSelected()
      if (this.media.length === 0) {
        this.onReloadMedia()
      }
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

      if (id <= 0) {
        return
      }

      await destroyCategory(id)
      this.$message.success(getMessage('destroyed'))
      removeFromNested(this.categories, id)
    },
    allowDrag({ data }) {
      return data.id > 0
    },
    allowDrop({ data: source }, { data: target }, type) {
      // 不能拖放到 所有 和 无分类 下
      if (target.id <= 0) {
        return false
      }
      // 如果目标没有父级，则可以拖放到其前后（相当于变为一级分类）或内部
      if (target.parent_id === 0) {
        return true
      }
      // 同级之间，只能拖放到其内部，不能拖放到前后（相当于拖放排序）
      if ((source.parent_id === target.parent_id) && (type !== 'inner')) {
        return false
      }

      return true
    },
    onNodeDrop({ data: source }, { data: target }, type) {
      // 目标分类 id
      let id = 0
      if (type === 'inner') { // 如果是放到目标内部，则 parent_id 为目标 id
        id = target.id
      } else { // 否则，拖放到目标的前后，则 parent_id 为目标的 parent_id（与目标同级）
        id = target.parent_id
      }

      this.updateCategory(source, {
        parent_id: id,
      })
    },
    async updateCategory(category, data) {
      const res = await updateCategory(category.id, data)
      category.name = res.data.name
      this.$message.success(getMessage('updated'))
    },
    async onSaveCategory() {
      if (!this.canSaveCategory) {
        return
      }

      if (this.categoryEdit) {
        await this.updateCategory(this.currentCategory, {
          name: this.categoryName,
        })
      } else {
        let parentId = this.categoryParentId
        // -1 为所有分类，其下级为 一级 分类
        parentId = parentId === -1 ? 0 : parentId
        const { data } = await storeCategory({
          parent_id: parentId,
          name: this.categoryName,
        })
        if (parentId) {
          this.$refs.tree.append(data, this.categoryParentId)
        } else {
          this.categories.push(data)
        }
        this.$message.success(getMessage('created'))
      }

      this.categoryDialog = false
    },
    onOpenCategoryDialog(editMode = true) {
      this.categoryEdit = editMode

      if (!this.canSaveCategory) {
        return
      }

      this.categoryName = editMode ? this.currentCategory.name : ''
      this.categoryParentId = this.currentCategoryId // 快照

      this.categoryDialog = true
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

  .el-icon-more {
    display: none;
  }
}
</style>
