<template>
  <router-link to="/" class="title" :title="appName">
    <div v-if="appLogo" class="flex-box logo-wrapper mr-2">
      <img :src="appLogo" class="logo">
    </div>
    <span
      v-show="!collapse || !appLogo"
      class="app-name"
    >{{ appName }}</span>
  </router-link>
</template>

<script>
import { mapGetters } from 'vuex'
import { SYSTEM_BASIC } from '@/libs/constants'
import { getUrl } from '@/libs/utils'

export default {
  name: 'SideMenuTitle',
  props: {
    collapse: Boolean,
  },
  computed: {
    ...mapGetters([
      'appName',
      'getConfig',
    ]),
    appLogo() {
      return getUrl(this.getConfig(SYSTEM_BASIC.SLUG + '.' + SYSTEM_BASIC.APP_LOGO_SLUG))
    },
  },
}
</script>

<style scoped lang="scss">
$logo-width: 44px;

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
