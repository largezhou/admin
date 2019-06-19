export default {
  computed: {
    editMode() {
      return !!this.resourceId
    },
    resourceId() {
      return this.$route.params.id
    },
  },
}
