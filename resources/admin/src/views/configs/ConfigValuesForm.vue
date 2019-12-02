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
import ConfigsForm from '@v/configs/ConfigsForm'

export default {
  name: 'ConfigValuesForm',
  components: {
    ConfigsForm,
  },
}
</script>
