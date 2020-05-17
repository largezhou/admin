<template>
  <div class="category">
    <a-input
      class="filter-input mb-1"
      :class="{ 'w-100': miniWidth }"
      placeholder="搜索分类"
      v-model="q"
    />
    <a-button-group class="category-actions mb-2" :class="{ 'w-100': miniWidth }">
      <loading-action :action="getCategories" disable-text-when-loading>刷新</loading-action>
      <a-button @click="onOpenDialog(false)">添加</a-button>
      <a-button :disabled="currentId <= 0" @click="onOpenDialog(true)">编辑</a-button>
      <lz-popconfirm
        :disabled="currentId <= 0"
        :overlay-style="{ width: '200px' }"
        :confirm="onDestroyCategory"
        title="所有子孙分类也将被删除!分类下的文件会被移动到“无分类”下。确认删除？"
      >
        <a-button type="danger" :disabled="currentId <= 0">删除</a-button>
      </lz-popconfirm>
    </a-button-group>
    <a-spin class="categories" :spinning="loading">
      <a-tree
        v-if="this.categories.length"
        :tree-data="categories"
        :replace-fields="treeOptions"
        default-expand-all
        :selected-keys="[currentId]"
        block-node
        @select="onSelect"
        draggable
        @drop="onDrop"
        :expanded-keys="expandedKeys"
        :auto-expand-parent="autoExpandParent"
        :filter-tree-node="onFilter"
        @expand="onExpand"
      />
    </a-spin>

    <a-modal
      :title="editMode ? '编辑分类' : '添加分类'"
      v-model="dialog"
      :footer="null"
      width="400px"
    >
      <lz-form
        disable-redirect
        :form.sync="form"
        :errors.sync="errors"
        :submit="onSave"
        in-dialog
        layout="vertical"
        enter-to-submit
        :edit-mode="editMode"
      >
        <lz-form-item label="父级" prop="parent_id">
          <a-select
            v-model="form.parent_id"
            show-search
            option-filter-prop="title"
            option-label-prop="title"
          >
            <a-select-option
              v-for="i of cateOptions"
              :key="i.id"
              :title="i.title"
            >
              {{ i.text }}
            </a-select-option>
          </a-select>
        </lz-form-item>
        <lz-form-item label="名称" required prop="name">
          <a-input v-model="form.name" focus/>
        </lz-form-item>
        <lz-form-item label="文件夹" prop="folder">
          <a-input v-model="form.folder"/>
        </lz-form-item>
      </lz-form>
    </a-modal>
  </div>
</template>

<script>
import {
  destroyCategory,
  getCategories,
  storeCategory,
  updateCategory,
} from '@/api/system-media'
import _get from 'lodash/get'
import LzPopconfirm from '@c/LzPopconfirm'
import {
  getMessage,
  nestedToSelectOptions,
  removeFromNested,
} from '@/libs/utils'
import LoadingAction from '@c/LoadingAction'
import LzForm from '@c/LzForm/index'
import LzFormItem from '@c/LzForm/LzFormItem'

const getParentKey = (id, tree) => {
  let parentKey
  for (let i = 0; i < tree.length; i++) {
    const node = tree[i]
    if (node.children) {
      if (node.children.some(item => item.id === id)) {
        parentKey = node.id
      } else if (getParentKey(id, node.children)) {
        parentKey = getParentKey(id, node.children)
      }
    }
  }
  return parentKey
}

export default {
  name: 'Category',
  components: {
    LzPopconfirm,
    LoadingAction,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      treeOptions: {
        children: 'children',
        title: 'name',
        key: 'id',
      },

      q: '', // 分类筛选
      expandedKeys: [],
      autoExpandParent: true,

      categories: [],
      loading: false,
      current: null,

      editMode: false, // 编辑或者添加时，用的同一个弹框
      dialog: false,

      form: {
        parent_id: 0,
        name: '',
        folder: '',
      },
      errors: {},
    }
  },
  computed: {
    currentId() {
      return _get(this.current, 'id', -1)
    },
    miniWidth() {
      return this.$store.state.miniWidth
    },
    canSave() {
      // 添加 或者 编辑时，ID 大于 0
      return !this.editMode || this.currentId > 0
    },
    cateOptions() {
      return nestedToSelectOptions(this.categories.slice(2), {
        title: 'name',
      })
    },
    flattenCategories() {
      const flatten = []
      const loop = (items) => {
        items.forEach((i) => {
          flatten.push({
            id: i.id,
            name: i.name,
          })
          loop(i.children || [])
        })
      }

      loop(this.categories)

      return flatten
    },
  },
  async created() {
    await this.getCategories()
    this.initSelected()
  },
  methods: {
    async getCategories() {
      this.loading = true
      try {
        const { data } = await getCategories()

        // 追加两个伪分类，但是同步到父组件时，要去掉
        this.categories = [
          {
            id: -1,
            name: '所有',
          },
          {
            id: 0,
            name: '无分类',
          },
          ...data,
        ]
      } finally {
        this.loading = false
      }
    },
    async initSelected() {
      await this.$nextTick()
      this.current = this.categories[0]
    },
    onOpenDialog(editMode = true) {
      this.editMode = editMode

      if (!this.canSave) {
        return
      }

      this.form = {
        parent_id: editMode
          ? this.current.parent_id
          : (this.currentId < 0) ? 0 : this.currentId,
        name: editMode ? this.current.name : '',
        folder: editMode ? this.current.folder : '',
      }

      this.dialog = true
    },
    async onDestroyCategory() {
      const id = this.currentId

      if (id <= 0) {
        return
      }

      await destroyCategory(id)
      this.$message.success(getMessage('destroyed'))

      this.initSelected()

      removeFromNested(this.categories, id)
    },
    onSelect(selectedKeys, e) {
      if (selectedKeys.length) {
        this.current = e.node.dataRef
      }
    },
    async updateCategory(category, data, showValidationMsg = true) {
      const res = await updateCategory(category.id, data)
        .setConfig({
          showValidationMsg,
          validationForm: showValidationMsg ? null : this,
        })

      category.name = res.data.name
      category.parent_id = res.data.parent_id
    },
    async onSave($form) {
      if (!this.canSave) {
        return
      }

      if (this.editMode) {
        await this.updateCategory(this.current, this.form, false)
      } else {
        await storeCategory(this.form).setConfig({ validationForm: this })
      }

      if ($form.stay) {
        $form.onReset()
      } else {
        this.dialog = false
      }

      this.getCategories()
    },
    onDrop({ dragNode, node, dropToGap }) {
      const source = dragNode.dataRef
      const target = node.dataRef

      if (source.id <= 0 || target.id <= 0) {
        return
      }

      // 同级之间，只能拖放到其内部，不能拖放到前后（相当于拖放排序）
      if ((source.parent_id === target.parent_id) && dropToGap) {
        return
      }

      // 拖放到自己的父级内，相当于没拖动
      if (source.parent_id === target.id && !dropToGap) {
        return
      }

      this.dropped(source, target, dropToGap)
    },
    async dropped(source, target, dropToGap) {
      // 父级分类 id
      let id = 0
      // 如果拖动到间隙，则与目标同级
      if (dropToGap) {
        id = target.parent_id
      } else {
        id = target.id
      }

      await this.updateCategory(source, { parent_id: id }, true)
      // 直接重新请求数据，省事，，，
      this.getCategories()
    },
    onFilter(node) {
      return this.q && node.title.indexOf(this.q) > -1
    },
    onExpand(expandedKeys) {
      this.expandedKeys = expandedKeys
      this.autoExpandParent = false
    },
    setExpandedKeys() {
      this.expandedKeys = this.flattenCategories
        .map((item) => {
          if (item.name.indexOf(this.q) > -1) {
            return getParentKey(item.id, this.categories)
          }
          return null
        })
        .filter((item, i, self) => item && self.indexOf(item) === i)

      this.autoExpandParent = true
    },
  },
  watch: {
    q: {
      handler() {
        this.setExpandedKeys()
      },
      immediate: true,
    },
    flattenCategories() {
      this.setExpandedKeys()
    },
    current: {
      handler(newVal) {
        this.$emit('select', newVal)
      },
      immediate: true,
    },
    categories: {
      handler(newVal) {
        // 去掉前面两个 伪分类
        this.$emit('categories-change', newVal.slice(2))
      },
      deep: true,
    },
    dialog(newVal) {
      if (!newVal) {
        this.errors = {}
        this.form = {
          parent_id: 0,
          name: '',
        }
      }
    },
  },
}
</script>

<style scoped lang="less">
.category-actions {
  display: flex;

  > * {
    width: 25%;
    padding: 0;
  }

  > span button {
    width: 100%;
    padding: 0;
  }
}

.category {
  display: flex;
  flex-direction: column;
}

.categories {
  overflow: auto;
  flex: 1;
}
</style>
