<template>
  <el-card class="create">
    <template #header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <configs-form/>
    </el-row>
  </el-card>
</template>

<script>
import ConfigsForm from '@c/ConfigsForm'

export default {
  name: 'SystemBasic',
  components: {
    ConfigsForm,
  },
}
</script>
