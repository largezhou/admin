const path = require('path')
const LiveReloadPlugin = require('webpack-livereload-plugin')

function pathResolve() {
  return path.resolve(__dirname, ...arguments)
}

const inDev = process.env.NODE_ENV === 'development'
const devSuffix = inDev ? '-dev' : ''

module.exports = {
  outputDir: pathResolve(`./public/lz-admin${devSuffix}`),
  publicPath: `/lz-admin${devSuffix}`,
  lintOnSave: false,
  configureWebpack: {
    entry: pathResolve('./resources/src/main.js'),
    resolve: {
      alias: {
        '@': pathResolve('./resources/src'),
      },
    },
    plugins: [
      new LiveReloadPlugin({
        delay: 500,
      }),
    ],
  },
  chainWebpack(config) {
    config
      .plugin('html')
      .tap((args) => {
        const o = args[0]
        o.template = pathResolve('./resources/src/template/admin.blade.php')
        o.filename = pathResolve(`./resources/views/admin${devSuffix}.blade.php`)

        return args
      })

    config.plugins.delete('copy')
  },
}
