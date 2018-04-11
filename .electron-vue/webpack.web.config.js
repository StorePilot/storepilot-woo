'use strict'

process.env.BABEL_ENV = 'web'

const path = require('path')
const webpack = require('webpack')
const fs = require('fs')
const mkdirp = require('mkdirp')

const BabelMinifyWebpackPlugin = require('babel-minify-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const zip = new require('zip-dir')
const WebpackCustomizer = require('webpack-customizer')

let webConfig = {
  devtool: '#cheap-module-eval-source-map',
  entry: {
    web: path.join(__dirname, '../src/renderer/main.js')
  },
  module: {
    rules: [
      {
        test: /\.(js|vue)$/,
        enforce: 'pre',
        exclude: /node_modules/,
        use: {
          loader: 'eslint-loader',
          options: {
            formatter: require('eslint-friendly-formatter')
          }
        }
      },
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: 'css-loader'
        })
      },
      {
        test: /\.html$/,
        use: 'vue-html-loader'
      },
      {
        test: /\.js$/,
        use: 'babel-loader',
        include: [ path.resolve(__dirname, '../src/renderer') ],
        exclude: /node_modules/
      },
      {
        test: /\.vue$/,
        use: {
          loader: 'vue-loader',
          options: {
            extractCSS: true,
            loaders: {
              sass: 'vue-style-loader!css-loader!sass-loader?indentedSyntax=1',
              scss: 'vue-style-loader!css-loader!sass-loader'
            }
          }
        }
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        use: {
          loader: 'url-loader',
          query: {
            limit: 10000,
            name: 'imgs/[name].[ext]'
          }
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        use: {
          loader: 'url-loader',
          query: {
            limit: 10000,
            name: 'fonts/[name].[ext]'
          }
        }
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin('styles.css'),
    new HtmlWebpackPlugin({
      filename: 'index.html',
      template: path.resolve(__dirname, '../src/index.ejs'),
      minify: {
        collapseWhitespace: true,
        removeAttributeQuotes: true,
        removeComments: true
      },
      nodeModules: false
    }),
    new webpack.DefinePlugin({
      'process.env.IS_WEB': 'true'
    }),
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NoEmitOnErrorsPlugin()
  ],
  output: {
    filename: '[name].js',
    path: path.join(__dirname, '../dist/web/app')
  },
  resolve: {
    alias: {
      '@': path.join(__dirname, '../src/renderer'),
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['.js', '.vue', '.json', '.css']
  },
  target: 'web'
}

/**
 * Adjust webConfig for production settings
 */
if (process.env.NODE_ENV === 'production') {
  webConfig.devtool = ''

  webConfig.plugins.push(
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': '"production"'
    }),
    new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /en/),
    new BabelMinifyWebpackPlugin(),
    new WebpackCustomizer({
      hook: 'after-emit',
      action () {
        let replace = (from, search, repl, to = from, includes = [], excludes = []) => {
          // Replace string in file and save
          let replaceFile = (fileFrom, search, repl, fileTo = fileFrom) => {
            if (
              // Filetypes which is replaced before copy, other filetypes will be copied only
              fileFrom.indexOf('.html') !== -1 ||
              fileFrom.indexOf('.js') !== -1 ||
              fileFrom.indexOf('.css') !== -1 ||
              fileFrom.indexOf('.php') !== -1 ||
              fileFrom.indexOf('.txt') !== -1 ||
              fileFrom.indexOf('.pot') !== -1 ||
              fileFrom.indexOf('.po') !== -1 ||
              fileFrom.indexOf('.md') !== -1
            ) {
              fs.readFile(fileFrom, 'utf-8', (err, content) => {
                if (err) {
                  return console.log(err)
                }
                content = content.replace(search, repl)
                if (fileTo.indexOf('.') === -1) {
                  fileTo += (fileTo[(fileTo.length - 1)] === '/' ? '' : '/') + path.basename(fileFrom)
                }
                mkdirp(path.dirname(fileTo), err => {
                  if (err) {
                    return console.log(err)
                  }
                  fs.writeFile(fileTo, content, 'utf-8', err => {
                    if (err) {
                      return console.log(err)
                    }
                  })
                })
              })
            } else {
              if (fileTo.indexOf('.') === -1) {
                fileTo += (fileTo[(fileTo.length - 1)] === '/' ? '' : '/') + path.basename(fileFrom)
              }
              mkdirp(path.dirname(fileTo), err => {
                if (err) {
                  return console.log(err)
                }
                fs.createReadStream(fileFrom).pipe(fs.createWriteStream(fileTo))
              })
            }
          }
          // If dir, read dir recursively, else replace in file & save
          if (fs.lstatSync(from).isDirectory()) {
            fs.readdir(from, (err, filenames) => {
              if (err) {
                return console.log(err)
              }
              filenames.forEach(filename => {
                if (!excludes.includes(from + filename)) {
                  replace(
                    from + (from[(from.length - 1)] === '/' ? '' : '/') + filename,
                    search,
                    repl,
                    to + (to[(to.length - 1)] === '/' ? '' : '/') + filename,
                    [],
                    excludes
                  )
                }
              })
              includes.forEach(p => {
                replace(p, search, repl, to, [], excludes)
              })
            })
          } else if (fs.lstatSync(from).isFile()) {
            replaceFile(from, search, repl, to)
            includes.forEach(p => {
              replace(p, search, repl, to, [], excludes)
            })
          }
        }
        replace(
          path.join(__dirname, '../licensing/'),
          /SP_VERSION_REPLACE/g,
          require('../package.json').version,
          path.join(__dirname, '../dist/web/licensing/'),
          [],
          // Ignore
          [
          ]
        )
        replace(
          path.join(__dirname, '../includes/'),
          /SP_VERSION_REPLACE/g,
          require('../package.json').version,
          path.join(__dirname, '../dist/web/includes/'),
          [],
          // Ignore
          [
            path.join(__dirname, '../includes/dev'),
            path.join(__dirname, '../includes/.DS_Store'),
            path.join(__dirname, '../includes/storepilot.php'),
            path.join(__dirname, '../includes/readme.txt')
          ]
        )
        replace(
          path.join(__dirname, '../includes/storepilot.php'),
          /SP_VERSION_REPLACE/g,
          require('../package.json').version,
          path.join(__dirname, '../dist/web/'),
          [
            path.join(__dirname, '../includes/readme.txt')
          ]
        )
        setTimeout(function () {
          zip(path.join(__dirname, '../dist/web/'), {
              saveTo: path.join(__dirname, '../dist/web/storepilot.zip'),
              filter: (path, stat) => !/\.html$/.test(path)
            }, (err, buffer) => {
              if (err) {
                return console.log(err)
              }
            }
          )
          setTimeout(function () {
            let rmDir = function (dirPath) {
              if (fs.statSync(dirPath).isFile()) {
                fs.unlinkSync(dirPath)
              } else {
                try {
                  var files = fs.readdirSync(dirPath)
                } catch (e) {
                  return
                }
                if (files.length > 0)
                  for (var i = 0; i < files.length; i++) {
                    var filePath = dirPath + '/' + files[i]
                    if (fs.statSync(filePath).isFile()) {
                      fs.unlinkSync(filePath)
                    } else {
                      rmDir(filePath)
                    }
                  }
                fs.rmdirSync(dirPath)
              }
            }
            fs.readdir(path.join(__dirname, '../dist/web/'), (err, files) => {
              if (err) {
                return console.log(err)
              }
              for (const file of files) {
                if (file.indexOf('storepilot.zip') === -1) {
                  rmDir(path.join(__dirname, '../dist/web/' + file))
                }
              }
            })
          }, 100)
        }, 100)
      }
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true
    })
  )
}

module.exports = webConfig
