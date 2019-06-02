<template>
  <el-card class="create">
    <template v-slot:header>
      <span>添加菜单</span>
    </template>
    <el-form ref="form" :model="form" label-width="100px">
      <el-form-item label="父级菜单" :error="errors.parent_id" prop="parent_id">
        <el-select
          v-model="form.parent_id"
          filterable
          clearable
          placeholder="一级"
        >
          <el-option
            v-for="i of menuOptions"
            :key="i.id"
            :label="i.title"
            :value="i.id"
          >
            <span>{{ i.text }}</span>
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item
        label="标题"
        required
        :error="errors.title"
        prop="title"
      >
        <el-input v-model="form.title"/>
      </el-form-item>
      <el-form-item label="地址" :error="errors.uri" prop="uri">
        <el-input v-model="form.uri">
          <template slot="prepend">/admin/</template>
        </el-input>
      </el-form-item>
      <el-form-item label="图标" :error="errors.icon" prop="icon">
        <el-input v-model="form.icon" style="width: 200px;"/>
      </el-form-item>
      <el-form-item label="排序" :error="errors.order" prop="order">
        <el-input-number v-model="form.order" :min="-9999" :max="9999"/>
      </el-form-item>
      <el-form-item label="显示在菜单" :error="errors.is_menu" prop="is_menu">
        <el-switch
          v-model="form.is_menu"
          active-text="显示"
          inactive-text="隐藏"
        />
      </el-form-item>
      <el-form-item label="缓存" :error="errors.cache" prop="cache">
        <el-switch
          v-model="form.cache"
          active-text="缓存"
          inactive-text="不缓存"
        />
      </el-form-item>
      <el-form-item>
        <loading-action type="primary" :action="onSubmit">添加</loading-action>
        <el-button @click="onReset">重置</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>
<script>
import { buildMenuOptions, handleValidateErrors } from '@/libs/utils'
import { getMenus, storeMenu } from '@/api/admin-menus'

export default {
  data() {
    return {
      form: {
        parent_id: 0,
        title: '',
        uri: '',
        icon: '',
        order: 0,
        cache: false,
        is_menu: false,
      },
      errors: {},
      menus: [],
    }
  },
  computed: {
    menuOptions() {
      return buildMenuOptions(this.menus)
    },
  },
  created() {
    this.getMenus()
  },
  methods: {
    async onSubmit() {
      this.errors = {}
      try {
        await storeMenu(this.form)
        this.$router.push('/menus')
      } catch (e) {
        this.errors = handleValidateErrors(e)
      }
    },
    onReset() {
      this.$refs.form.resetFields()
    },
    async getMenus() {
      const { data } = await getMenus()
      this.menus = data
    },
  },
}
</script>

<style scoped lang="scss">
.create {
  max-width: 800px;
}

.el-select {
  width: 300px;
}
</style>
