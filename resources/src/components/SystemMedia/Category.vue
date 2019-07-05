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
      <el-button
        :disabled="currentId === 0"
        size="mini"
        @click="onOpenDialog(false)"
      >
        添加
      </el-button>
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
      :width="miniWidth ? '90%' : '400px'"
      @keydown.enter.native="$refs.saveConfirm.onAction"
      append-to-body
    >
      <el-input v-model="inputName" autocomplete="off" placeholder="请输入分类名称"/>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialog = false">取消</el-button>
        <loading-action
          :disabled="!inputName"
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
  getMessage,
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
      inputName: '',
      parentId: 0, // 添加分类时，父级 id 快照
      dialog: false,
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
      const id = this.currentId
      // 所有 和 无分类 不能编辑
      if (this.editMode && id <= 0) {
        return false
      }
      // 无分类 不能添加子分类
      if (!this.editMode && id === 0) {
        return false
      }

      return true
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

      this.inputName = editMode ? this.current.name : ''
      this.parentId = this.currentId // 快照

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
        .config({ showValidationMsg: true })
      category.name = res.data.name
      this.$message.success(getMessage('updated'))
    },
    async onSave() {
      if (!this.canSave || !this.inputName) {
        return
      }

      if (this.editMode) {
        await this.updateCategory(this.current, {
          name: this.inputName,
        })
      } else {
        let parentId = this.parentId
        // -1 为所有分类，其下级为 一级 分类
        parentId = parentId === -1 ? 0 : parentId
        const { data } = await storeCategory({
          parent_id: parentId,
          name: this.inputName,
        }).config({ showValidationMsg: true })

        // 手动先加一个 children 字段，不然后面给该节点添加子节点时，
        // el-tree 组件自动加的 children 属性，没有响应式
        data.children = []
        this.$refs.tree.append(data, parentId)

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

.side-tree {
  min-width: calc(100% - 15px);
  padding: 0 15px 15px 0;
  display: inline-block;
}

/deep/ {
  .scroll-wrapper {
    height: calc(100% - 60px);
  }

  .el-tree-node__content {
    height: 30px;
  }
}

</style>
