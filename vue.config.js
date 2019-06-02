const path = require('path')
const LiveReloadPlugin = require('webpack-livereload-plugin')

function pathResolve() {
  return path.resolve(__dirname, ...arguments)
}

const inDev = process.env.NODE_ENV === 'development'
const adminFolder = `admin${inDev ? '-dev' : ''}`

module.exports = {
  outputDir: pathResolve('public/', adminFolder),
  publicPath: '/' + adminFolder,
  lintOnSave: false,
  configureWebpack: {
    entry: pathResolve('resources/src/main.js'),
    plugins: [
      new LiveReloadPlugin(),
    ],
  },
  chainWebpack(config) {
    config
      .plugin('html')
      .tap((args) => {
        const o = args[0]
        o.template = pathResolve('resources/src/template/index.html')
        o.filename = pathResolve(`public/${adminFolder}/index.html`)

        return args
      })

    config.plugins.delete('copy')

    config
      .resolve
      .alias
      .set('@', pathResolve('resources/src'))
      .set('@c', pathResolve('resources/src/components'))
      .set('@v', pathResolve('resources/src/views'))

    // set svg-sprite-loader
    config.module
      .rule('svg')
      .exclude.add(pathResolve('resources/src/icons'))
      .end()
    config.module
      .rule('icons')
      .test(/\.svg$/)
      .include.add(pathResolve('resources/src/icons'))
      .end()
      .use('svg-sprite-loader')
      .loader('svg-sprite-loader')
      .options({
        symbolId: 'icon-[name]',
      })
      .end()
  },
}
