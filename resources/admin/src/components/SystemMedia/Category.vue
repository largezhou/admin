<template>
  <div>
    <el-input
      class="filter-input mb-1"
      :class="{ 'w-100': miniWidth }"
      placeholder="搜索分类"
      size="small"
      v-model="q"
    />
    <el-button-group class="category-actions mb-2" :class="{ 'w-100': miniWidth }">
      <loading-action size="mini" :action="getCategories">刷新</loading-action>
      <el-button size="mini" @click="onOpenDialog(false)">添加</el-button>
      <el-button
        :disabled="currentId <= 0"
        size="mini"
        @click="onOpenDialog(true)"
      >
        编辑
      </el-button>
      <pop-confirm
        :disabled="currentId <= 0"
        size="mini"
        type="danger"
        :confirm="onDestroyCategory"
        notice="所有子孙分类也将被删除!分类下的文件会被移动到“无分类”下。确认删除？"
      >
        删除
      </pop-confirm>
    </el-button-group>
    <el-scrollbar v-loading="loading" class="scroll-wrapper">
      <div class="side-tree">
        <el-tree
          :expand-on-click-node="false"
          :data="categories"
          :props="treeOptions"
          default-expand-all
          :filter-node-method="filter"
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

    <el-dialog
      :title="editMode ? '编辑分类' : '添加分类'"
      :visible.sync="dialog"
      width="400px"
      @keydown.enter.native="$refs.saveConfirm.onAction"
      append-to-body
    >
      <el-form :model="form" label-position="top">
        <el-form-item :error="errors.parent_id" label="父级">
          <el-select
            v-model="form.parent_id"
            filterable
            clearable
          >
            <el-option
              v-for="i of cateOptions"
              :key="i.id"
              :label="i.title"
              :value="i.id"
            >
              <span>{{ i.text }}</span>
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :error="errors.name" class="mb-0" label="名称">
          <el-input
            v-model="form.name"
            autocomplete="off"
            autofocus
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialog = false">取消</el-button>
        <loading-action
          ref="saveConfirm"
          type="primary"
          :action="onSave"
        >
          确定
        </loading-action>
      </div>
    </el-dialog>
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
import PopConfirm from '@c/PopConfirm'
import {
  getMessage, nestedToSelectOptions,
} from '@/libs/utils'

export default {
  name: 'Category',
  components: {
    PopConfirm,
  },
  data() {
    return {
      treeOptions: {
        children: 'children',
        label: 'name',
      },

      q: '', // 分类筛选

      categories: [],
      loading: false,
      current: null,

      editMode: false, // 编辑或者添加时，用的同一个弹框
      dialog: false,

      form: {
        parent_id: 0,
        name: '',
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
  },
  created() {
    this.getCategories()
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

        await this.$nextTick()

        this.initSelected()
      } finally {
        this.loading = false
      }
    },
    async initSelected() {
      await this.$nextTick()
      this.$refs.tree.setCurrentKey(-1)
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

      this.$refs.tree.remove(id)
    },
    filter(value, category) {
      if (!value) {
        return true
      }
      return category.name.indexOf(value) !== -1
    },
    onCurrentChange(category) {
      this.current = category
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
    async onNodeDrop({ data: source }, { data: target }, type) {
      // 目标分类 id
      let id = 0
      if (type === 'inner') { // 如果是放到目标内部，则 parent_id 为目标 id
        id = target.id
      } else { // 否则，拖放到目标的前后，则 parent_id 为目标的 parent_id（与目标同级）
        id = target.parent_id
      }

      try {
        await this.updateCategory(source, { parent_id: id }, true)
      } catch (e) {
        // 如果更新失败，则要恢复层次结构
        const tree = this.$refs.tree
        tree.remove(source)
        tree.append(source, source.parent_id)
      }
    },
    async updateCategory(category, data, showValidationMsg = true) {
      const res = await updateCategory(category.id, data)
        .setConfig({
          showValidationMsg,
          validationForm: showValidationMsg ? null : this,
        })
      this.$message.success(getMessage('updated'))

      category.name = res.data.name
      category.parent_id = res.data.parent_id
    },
    async onSave() {
      if (!this.canSave) {
        return
      }

      const tree = this.$refs.tree
      const newParentId = this.form.parent_id

      this.errors = {}
      if (this.editMode) {
        const oldParentId = this.current.parent_id
        await this.updateCategory(this.current, this.form, false)
        // 如果编辑后，父级分类改了，则要移动分类
        if (oldParentId !== newParentId) {
          tree.remove(this.current)
          tree.append(this.current, newParentId)
        }
      } else {
        const { data } = await storeCategory(this.form)
          .setConfig({ validationForm: this })

        // 手动先加一个 children 字段，不然后面给该节点添加子节点时，
        // el-tree 组件自动加的 children 属性，没有响应式
        data.children = []
        tree.append(data, newParentId)

        this.$message.success(getMessage('created'))
      }

      this.dialog = false
    },
  },
  watch: {
    q(val) {
      this.$refs.tree.filter(val)
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

<style scoped lang="scss">
.filter-input,
.category-actions {
  width: 185px;
}

.category-actions {
  display: flex;

  > .el-button {
    width: 25%;
    flex-grow: 1;
    padding: 7px 0;
  }
}

.side-tree {
  min-width: calc(100% - 15px);
  padding: 0 15px 15px 0;
  display: inline-block;
}

::v-deep {
  .scroll-wrapper {
    height: calc(100% - 60px);
  }

  .el-tree-node__content {
    height: 30px;
  }
}

</style>
