<template>
  <el-card class="system-media" :class="{ 'mini-width': miniWidth }" shadow="never">
    <el-container class="body">
      <el-aside v-if="!miniWidth" class="aside" width="221px">
        <category
          class="h-100"
          ref="category"
          @select="onCategorySelect"
          @categories-change="onCategoriesChange"
        />
      </el-aside>

      <el-dialog
        class="categories-dialog"
        title="选择分类"
        :visible.sync="categoriesDialog"
        width="90%"
        append-to-body
      >
        <category
          v-if="miniWidth"
          class="h-100"
          ref="category"
          @select="onCategorySelect"
          @categories-change="onCategoriesChange"
        />
      </el-dialog>

      <el-container>
        <el-header>
          <collapse-button-group>
            <loading-action :action="onReloadMedia">刷新</loading-action>
            <el-button :disabled="!anySelected" @click="movingDialog = true">移动</el-button>
            <!-- 没有设置默认多选时，就可以切换多选，switch 组件放这里，样式懒得调 -->
            <el-button
              :type="multiple ? 'primary' : ''"
              v-if="defaultMultiple === undefined"
              @click="multiple = !multiple"
            >
              多选
            </el-button>
            <pop-confirm
              notice="物理文件也有可能会被删除！确认删除？"
              type="danger"
              :disabled="!anySelected"
              :confirm="onDestroyMedia"
            >
              删除
            </pop-confirm>
          </collapse-button-group>
          <el-button v-if="miniWidth" class="ml-1" @click="categoriesDialog = true">选择分类</el-button>
        </el-header>

        <el-main
          v-loading="mediaLoading || uploading"
          :element-loading-text="uploadingText"
        >
          <div class="h-100">
            <el-scrollbar class="h-100">
              <files
                ref="media"
                :media="media"
                :multiple="multiple"
                :selected.sync="selected"
                :ext="defaultExt"
              />
            </el-scrollbar>
          </div>
        </el-main>
        <el-footer class="footer">
          <collapse-button-group class="footer-actions">
            <el-button :disabled="currentCategoryId <= 0" @click="onClickUpload">上传</el-button>
            <el-button
              :disabled="!anySelected"
              @click="clearSelected"
            >
              清空 {{ this.selectedCount ? `(${this.selectedCount})` : '' }}
            </el-button>
            <el-button
              :disabled="!!defaultExt"
              @click="onOpenExtDialog"
              :title="ext"
            >
              {{ ext ? '已筛选' : '筛选' }}
            </el-button>
            <slot name="actions" v-bind="getThis"/>
          </collapse-button-group>
          <flex-spacer/>
          <pagination
            :page="page"
            layout="total, pager"
            :auto-push="false"
            @current-change="onPageChange"
            :pager-count="5"
            hide-on-single-page
          />
        </el-footer>
      </el-container>
    </el-container>

    <el-dialog
      v-if="!defaultExt"
      title="筛选类型"
      :visible.sync="extDialog"
      width="400px"
      @keydown.enter.native="onExtFilter"
      append-to-body
    >
      <el-input
        autofocus
        v-model="extTemp"
        autocomplete="off"
        placeholder="多个之间用英文逗号隔开"
      />
      <div slot="footer" class="dialog-footer">
        <el-button @click="extDialog = false">取消</el-button>
        <el-button type="primary" @click="onExtFilter">确定</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="移动文件"
      :visible.sync="movingDialog"
      width="400px"
      append-to-body
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

    <el-upload
      :disabled="currentCategoryId <= 0"
      ref="upload"
      style="display: none"
      multiple
      action="#"
      :http-request="storeMedia"
      :show-file-list="false"
      :on-change="onUploadChange"
      :before-upload="beforeUpload"
      :accept="'.' + (defaultExt ? defaultExt : '').replace(/,/g, ',.')"
    />
  </el-card>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import {
  batchDestroyMedia,
  batchUpdateMedia,
  getCategoryMedia,
  getMedia,
  storeMedia,
} from '@/api/system-media'
import _get from 'lodash/get'
import FlexSpacer from '@c/FlexSpacer'
import Pagination from '@c/Pagination'
import {
  debounceMsg,
  getExt,
  getMessage,
  nestedToSelectOptions,
} from '@/libs/utils'
import _differenceBy from 'lodash/differenceBy'
import Category from './Category'
import Files from '@c/SystemMedia/Files'
import CollapseButtonGroup from '@c/CollapseButtonGroup'

export default {
  name: 'SystemMedia',
  components: {
    CollapseButtonGroup,
    Files,
    Category,
    Pagination,
    FlexSpacer,
    PopConfirm,
  },
  data() {
    return {
      categories: [],
      currentCategory: null,

      media: [],
      mediaLoading: false,
      page: null,

      ext: this.defaultExt || '', // 只做查询筛选，不做强制限制
      extTemp: '', // 弹框中输入时，未确认的值
      extDialog: false,

      selected: [],

      movingDialog: false,
      moving: false,
      movingTarget: '',

      uploading: false,
      uploadCount: 0,
      uploadFail: 0,
      uploadSuccess: 0,
      uploadInvalid: 0,

      // 作为管理页面使用时，可以切换
      multiple: this.defaultMultiple === undefined
        ? false
        : this.defaultMultiple,

      categoriesDialog: false,
    }
  },
  props: {
    /**
     * 设置了该值，就不能再手动筛选了，
     * 并强制只能上传和选择这些类型的文件
     */
    defaultExt: {
      type: String,
      default: '',
    },
    /**
     * 多选，同上
     * 作为文件选择器使用时，设置就不能切换了
     */
    defaultMultiple: {
      type: Boolean,
      default: undefined,
    },
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
    categoriesSelectOptions() {
      return nestedToSelectOptions(this.categories, {
        title: 'name',
        firstLevel: null,
      })
    },
    uploadingText() {
      return this.uploading
        ? `正在上传中 (${this.uploadSuccess} / ${this.uploadCount})`
        : ''
    },
    getThis() {
      return this
    },
    extArray() {
      return this.ext.split(',')
    },
  },
  async created() {
    await this.getMedia()
  },
  methods: {
    async getMedia(categoryId = -1, page) {
      this.mediaLoading = true
      let data
      const params = {
        page,
        ext: this.ext || undefined,
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
    onPageChange(page) {
      this.getMedia(this.currentCategoryId, page)
    },
    async onReloadMedia() {
      await this.getMedia(this.currentCategoryId)
    },
    onExtFilter() {
      if (this.defaultExt) {
        return
      }

      this.ext = this.extTemp
      this.extDialog = false
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
      await batchUpdateMedia(data).setConfig({
        showValidationMsg: true,
      })
      this.movingDialog = false
      this.$message.success(getMessage('updated'))

      if (this.currentCategoryId === -1) { // 如果是在所有分类下，则只需要清除选中
        this.clearSelected()
      } else { // 否则，要从数据中清除
        this.moveSelected()
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
    onCategorySelect(category) {
      this.currentCategory = category
    },
    onCategoriesChange(categories) {
      this.categories = categories
    },
    onClickUpload() {
      const t = _get(this.$refs, 'upload.$refs.upload-inner')
      t && t.handleClick()
    },
    async storeMedia({ file }) {
      const id = this.currentCategoryId

      if (id <= 0) {
        return
      }
      const { data } = await storeMedia(id, file)
        .setConfig({ showValidationMsg: true })
      // 如果上传完后，没有切换分类，或者是所有分类
      // 则把数据怼到当前的文件列表前面
      if (id === this.currentCategoryId || this.currentCategoryId === -1) {
        this.media.unshift(data)
      }
    },
    onUploadChange(file, fileList) {
      if (file.status === 'success') { // 上传成功一个
        this.uploadSuccess++
      } else if (file.status === 'fail') { // 上传失败一个
        this.uploadFail++
      }

      // 上传完毕，清除上传列表
      if (
        this.uploadCount &&
        (this.uploadSuccess + this.uploadFail === this.uploadCount)
      ) {
        this.$msgbox({
          title: '上传完成',
          message: `上传成功 (${this.uploadSuccess})，失败 (${this.uploadFail})，无效 (${this.uploadInvalid})`,
        })

        this.$refs.upload.clearFiles()
        this.uploading = false
        this.uploadCount = 0
        this.uploadFail = 0
        this.uploadSuccess = 0
      }
    },
    beforeUpload(file) {
      const lt10M = file.size / 1024 / 1024 <= 10
      if (!lt10M) {
        // 有时候选择 N 个无效文件，会弹出一串提示，，所以搞个防抖，10ms 延迟
        // 还有其他更好的方法？
        debounceMsg('文件 不能大于 10240 KB')
      }

      // 如果指定了类型，则只能上传该类型的文件
      const validExt = !this.defaultExt || (this.extArray.indexOf(getExt(file.name)) !== -1)
      if (!validExt) {
        debounceMsg('文件类型只能是：' + this.defaultExt)
      }

      const res = lt10M && validExt

      if (res) { // 开始上传一个
        this.uploadCount++
        this.uploading = true
      } else { // 无效文件
        this.uploadInvalid++
      }

      return res
    },
    onOpenExtDialog() {
      if (this.defaultExt) {
        return
      }

      this.extDialog = true
    },
  },
  watch: {
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
    miniWidth(newVal) {
      if (!newVal) {
        this.categoriesDialog = false
      }
    },
    defaultMultiple(newVal) {
      this.multiple = newVal
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

$border: 1px solid $--color-info-light;
$padding-width: 15px;

.system-media {
  border: none;
  border-radius: 0;
}

.aside {
  border-right: $border;
  padding: $padding-width 0 $padding-width 20px;
  overflow: hidden;
}

.body {
  height: 550px;
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

::v-deep {
  .el-scrollbar__wrap {
    height: calc(100% + 17px);
  }

  .el-card__body {
    padding: 0;
  }

  .el-icon-more {
    display: none;
  }
}

.categories-dialog {
  ::v-deep {
    .el-dialog__body {
      height: 400px;
    }
  }
}

.mini-width {
  .footer {
    flex-direction: column-reverse;

    & > * {
      align-self: flex-start;
    }
  }

  .footer-actions {
    margin-top: 10px;
  }
}
</style>
