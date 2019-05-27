<template>
  <div class="layout">
    <Sider class="sider">
      <SideMenu/>
    </Sider>
    <Layout class="content-layout">
      <Header class="header"/>
      <Content class="content">
        <Breadcrumb class="bread-crumb" :test="breadCrumb">
          <BreadcrumbItem
            v-for="item of breadCrumb"
            :key="item.id"
          >
            {{ item.meta.title }}
          </BreadcrumbItem>
        </Breadcrumb>
        <Card>
          <div style="height: 600px">
            <keep-alive v-if="$route.meta && $route.meta.cache">
              <router-view/>
            </keep-alive>
            <router-view v-else/>
          </div>
        </Card>
      </Content>
    </Layout>
  </div>
</template>
<script>

import SideMenu from '@/layouts/Main/components/SideMenu'
import { homeName, homeRoute } from '@/router/routes'

export default {
  name: 'Main',
  components: {
    SideMenu,
  },
  computed: {
    breadCrumb() {
      const m = this.$route.matched.filter(i => i.name)
      if (this.$route.name !== homeName) {
        m.unshift(homeRoute)
      }
      return m
    },
  },
  created() {
    log('main created')
  },
}
</script>

<style scoped lang="scss">
.layout {
  background: #f5f7f9;
  position: relative;
  overflow: hidden;
}

.sider {
  position: fixed;
  height: 100vh;
  left: 0;
  overflow: auto;
}

.content-layout {
  margin-left: 200px;
}

.header {
  background: #fff;
  box-shadow: 0 2px 3px 2px rgba(0, 0, 0, .1);
}

.content {
  padding: 0 16px 16px 16px;
}

.bread-crumb {
  margin: 16px 0;
}
</style>
