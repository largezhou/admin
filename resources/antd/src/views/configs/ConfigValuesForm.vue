<template>
  <page-content center>
    <configs-form/>
  </page-content>
</template>

<script>
import ConfigsForm from './components/ConfigsForm'
import PageContent from '@c/PageContent'

export default {
  name: 'ConfigValuesForm',
  components: {
    PageContent,
    ConfigsForm,
  },
}
</script>
