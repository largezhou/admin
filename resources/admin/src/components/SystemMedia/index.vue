<template>
  <div
    class="system-media"
    :class="{
      'mini-width': miniWidth,
      'tiny-width': tinyWidth,
    }"
  >
    <div class="sider" v-if="!miniWidth">
      <category
        class="h-100"
        ref="category"
        @select="onCategorySelect"
        @categories-change="onCategoriesChange"
      />
    </div>
    <div class="content">
      <div class="header">
        <space>
          <a-button v-if="miniWidth" @click="categoriesDialog = true">选择分类</a-button>
          <loading-action :action="onReloadMedia">刷新</loading-action>
          <a-button :disabled="!anySelected" @click="movingDialog = true">移动</a-button>
          <a-button
            :type="multiple ? 'primary' : ''"
            v-if="defaultMultiple === undefined"
            @click="multiple = !multiple"
          >
            多选
          </a-button>
          <lz-popconfirm
            title="物理文件也有可能会被删除！确认删除？"
            type="danger"
            :disabled="!anySelected"
            :confirm="onDestroyMedia"
          >
            <a-button type="danger" :disabled="!anySelected">删除</a-button>
          </lz-popconfirm>
        </space>
      </div>
      <a-spin
        class="files"
        :class="{ 'files-empty': media.length === 0 }"
        :spinning="mediaLoading || uploading"
        :tip="uploadingText"
      >
        <files
          ref="media"
          :media="media"
          :multiple="multiple"
          :selected.sync="selected"
          :ext="defaultExt"
        />
        <a-empty v-show="media.length === 0 && !mediaLoading"/>
      </a-spin>
      <div class="footer">
        <space>
          <a-upload
            ref="upload"
            :custom-request="storeMedia"
            :before-upload="beforeUpload"
            :show-upload-list="false"
            :disabled="currentCategoryId <= 0"
            multiple
            :accept="'.' + (defaultExt ? defaultExt : '').replace(/,/g, ',.')"
            @change="onUploadChange"
          >
            <a-button
              :disabled="currentCategoryId <= 0"
              :title="currentCategoryId <= 0 ? '请先选择分类' : ''"
            >
              上传
            </a-button>
          </a-upload>
          <a-button
            :disabled="!anySelected"
            @click="clearSelected"
          >
            清空 {{ this.selectedCount ? `(${this.selectedCount})` : '' }}
          </a-button>
          <a-button
            :disabled="!!defaultExt"
            @click="onOpenExtDialog"
            :title="ext"
            :type="ext ? 'primary' : null"
          >
            {{ ext ? '已筛选' : '筛选' }}
          </a-button>
          <slot name="actions" v-bind="getThis"/>
        </space>
        <div class="flex-spacer"/>
        <lz-pagination
          style="height: auto;"
          :page="page"
          :auto-push="false"
          :show-quick-jumper="false"
          :show-size-changer="false"
          hide-on-single-page
          simple
          @current-change="onPageChange"
        />
      </div>
    </div>

    <a-modal
      v-if="!defaultExt"
      title="筛选类型"
      v-model="extDialog"
      width="400px"
      @ok="onExtFilter"
    >
      <a-input
        v-model="extTemp"
        @keydown.enter="onExtFilter"
        focus
        allow-clear
      />
    </a-modal>

    <a-modal
      title="移动文件"
      v-model="movingDialog"
      width="400px"
    >
      <a-select
        class="w-100"
        v-model="movingTarget"
        show-search
        option-filter-prop="title"
        option-label-prop="title"
      >
        <a-select-option
          v-for="i of categoriesSelectOptions"
          :key="i.id"
          :title="i.title"
        >
          {{ i.text }}
        </a-select-option>
      </a-select>

      <template #footer>
        <a-button @click="movingDialog = false">取消</a-button>
        <loading-action
          type="primary"
          :action="onMove"
          :disabled="!movingTarget"
        >
          移动
        </loading-action>
      </template>
    </a-modal>

    <a-modal
      v-if="miniWidth"
      title="选择分类"
      v-model="categoriesDialog"
      :footer="null"
    >
      <category
        class="h-100"
        ref="category"
        @select="onCategorySelect"
        @categories-change="onCategoriesChange"
      />
    </a-modal>
  </div>
</template>

<script>
import LzPopconfirm from '@c/LzPopconfirm'
import {
  batchDestroyMedia,
  batchUpdateMedia,
  getCategoryMedia,
  getMedia,
  storeMedia,
} from '@/api/system-media'
import _get from 'lodash/get'
import LzPagination from '@c/LzPagination'
import {
  debounceMsg,
  getExt,
  getMessage,
  nestedToSelectOptions,
} from '@/libs/utils'
import _differenceBy from 'lodash/differenceBy'
import Category from './Category'
import Files from '@c/SystemMedia/Files'
import Space from '@c/Space'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'SystemMedia',
  components: {
    Space,
    Files,
    Category,
    LzPagination,
    LzPopconfirm,
    LoadingAction,
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
    tinyWidth() {
      return this.$store.state.tinyWidth
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
    async storeMedia({ file, onSuccess, onError }) {
      const id = this.currentCategoryId

      if (id <= 0) {
        return
      }
      let res
      try {
        res = await storeMedia(id, file).setConfig({ showValidationMsg: true })
        onSuccess(res, null)
        // 如果上传完后，没有切换分类，或者是所有分类
        // 则把数据怼到当前的文件列表前面
        if (id === this.currentCategoryId || this.currentCategoryId === -1) {
          this.media.unshift(res.data)
        }
      } catch (e) {
        onError(e, res)
      }
    },
    onUploadChange({ file }) {
      if (file.status === 'done') { // 上传成功一个
        this.uploadSuccess++
      } else if (file.status === 'error') { // 上传失败一个
        this.uploadFail++
      }

      // 上传完毕，清除上传列表
      if (
        this.uploadCount &&
        (this.uploadSuccess + this.uploadFail === this.uploadCount)
      ) {
        this.$info({
          title: '上传完成',
          content: `上传成功 (${this.uploadSuccess})，失败 (${this.uploadFail})，无效 (${this.uploadInvalid})`,
        })

        this.$refs.upload.sFileList = []
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

<style scoped lang="less">
@import "~@/styles/vars";

@padding: 16px;

.system-media {
  min-width: 0;
  height: 550px;
  display: flex;
  border: @border-base;
  border-radius: @border-radius-base;
}

.sider {
  width: 220px;
  padding: @padding;
  border-right: @border-base;
}

.content {
  display: flex;
  flex: 1;
  flex-direction: column;
  min-width: 0;
}

.header, .footer {
  padding: @padding;
  overflow-x: auto;
  width: 100%;
  min-height: 65px;
}

.header {
  border-bottom: @border-base;
}

.footer {
  border-top: @border-base;
  display: flex;
  align-items: center;
}

.files {
  flex: 1;
  padding: @padding;
  overflow: auto;
}

.files-empty {
  ::v-deep {
    .ant-spin-container {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  }
}

.tiny-width {
  .footer {
    flex-direction: column-reverse;
    align-items: flex-start;
  }

  ::v-deep {
    .pagination {
      margin-bottom: 8px !important;
    }
  }
}
</style>
