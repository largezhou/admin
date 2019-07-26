<template>
  <router-link to="/" class="title" :title="appName">
    <div v-if="appLogo" class="flex-box logo-wrapper">
      <img :src="appLogo" class="logo">
    </div>
    <span
      v-show="!collapse || !appLogo"
      class="ml-2 app-name"
    >{{ appName }}</span>
  </router-link>
</template>

<script>
import { mapGetters } from 'vuex'
import { SYSTEM_BASIC } from '@/libs/constants'

export default {
  name: 'SideMenuTitle',
  props: {
    collapse: Boolean,
  },
  computed: {
    ...mapGetters([
      'appName',
      'appConfigs',
    ]),
    appLogo() {
      return this.appConfigs(SYSTEM_BASIC.APP_LOGO_SLUG)
    },
  },
}
</script>

<style scoped lang="scss">
$logo-width: 40px;

.title {
  display: flex;
  align-items: center;
  height: $logo-width;
  text-decoration: none;
}

.logo-wrapper {
  min-width: $logo-width;
  width: $logo-width;
  height: $logo-width;
  min-height: $logo-width;
  text-align: center;
}

.logo {
  max-width: 100%;
  max-height: 100%;
  border-radius: 4px;
}

.app-name {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #bfcbd9;
}
</style>
