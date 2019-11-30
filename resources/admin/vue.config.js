const path = require('path')
const LiveReloadPlugin = require('webpack-livereload-plugin')

function pathResolve() {
  return path.resolve(__dirname, ...arguments)
}

const inDev = process.env.NODE_ENV === 'development'
const adminFolder = `admin${inDev ? '-dev' : ''}`
const rootDir = 'src'
const publicFolder = '../../public'

module.exports = {
  outputDir: pathResolve(publicFolder, adminFolder),
  publicPath: '/' + adminFolder,
  lintOnSave: false,
  configureWebpack: {
    entry: pathResolve(rootDir, 'main.js'),
    plugins: [
      new LiveReloadPlugin(),
    ],
    watchOptions: {
      poll: (process.argv.indexOf('--poll') !== -1) ? 500 : false,
      aggregateTimeout: 500,
      ignored: /node_modules/,
    },
  },
  chainWebpack(config) {
    config
      .plugin('html')
      .tap((args) => {
        const o = args[0]
        o.template = pathResolve(rootDir, 'template/index.html')
        o.filename = pathResolve(publicFolder, adminFolder, 'index.html')

        return args
      })

    config.plugins.delete('copy')

    config
      .resolve
      .alias
      .set('@', pathResolve(rootDir))
      .set('@c', pathResolve(rootDir, 'components'))
      .set('@v', pathResolve(rootDir, 'views'))

    // set svg-sprite-loader
    config.module
      .rule('svg')
      .exclude.add(pathResolve(rootDir, 'icons'))
      .end()
    config.module
      .rule('icons')
      .test(/\.svg$/)
      .include.add(pathResolve(rootDir, 'icons'))
      .end()
      .use('svg-sprite-loader')
      .loader('svg-sprite-loader')
      .options({
        symbolId: 'icon-[name]',
      })
      .end()
  },
}
