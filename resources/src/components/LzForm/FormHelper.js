import { assignExists } from '@/libs/utils'

export default {
  provide() {
    return {
      view: this,
    }
  },
  data() {
    return {
      /**
       * 保存表单数据的键名
       */
      formField: 'form',
      /**
       * 编辑时，路由中的动态值
       */
      idField: 'id',
    }
  },
  computed: {
    editMode() {
      return !!this.resourceId
    },
    resourceId() {
      return this.$route.params[this.idField]
    },
  },
  created() {
    this.backupForm()
  },
  methods: {
    getResourceId(key = 'id') {
      return this.$route.params[key]
    },
    /**
     * 备份表单原始数据，路由切换时，用来重置表单
     */
    backupForm() {
      this.formBak = JSON.parse(JSON.stringify(this.$data[this.formField]))
    },
    /**
     * 填充表单数据
     *
     * @param data
     */
    fillForm(data) {
      this.$data[this.formField] = assignExists(this.$data[this.formField], data)
    },
  },
}
