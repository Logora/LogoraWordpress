const webpack = require('webpack');
const dotenv = require('dotenv');
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = (e, argv) => {
  let production = argv.mode === 'production'

  const env = dotenv.config().parsed;
  const envKeys = Object.keys(env).reduce((prev, next) => {
    prev[`process.env.${next}`] = JSON.stringify(env[next]);
    return prev;
  }, {});

  return {
    entry: {
      'js/app': path.resolve(__dirname, 'src/index.js'),
      'js/shortcode': path.resolve(__dirname, 'src/shortcode.js'),
    },

    output: {
      filename: '[name].js',
      path: path.resolve(__dirname, 'public'),
    },

    devtool: production ? '' : 'source-map',
  
    resolve: {
      extensions: [".js", ".jsx", ".json", ".html"],
    },
    optimization: {
        minimizer: [
          new TerserPlugin({
            terserOptions: {
              output: {
                comments: false,
              },
            },
          }),
        ],
    },
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          loader: 'babel-loader',
        },
        {
            test: /\.(css|less)$/,
            use: ["style-loader", "css-loader"]
        },
        {
            test: /\.(png|jpg|jpeg)$/,
            use: [{
                loader: 'url-loader',
                options: { 
                    limit: 100000,
                }
            }]
        },
        {
            test: /\.svg$/,
            use: [{
                loader: 'react-svg-loader'
            }]
        },
        {
          test: /\.(ttf|eot|woff(2)?)(\?[a-z0-9]+)?$/,
          loader: 'file-loader',
        },
        {
          test: /\.html$/,
          loader: "html-loader",
        },
        {
            test: /\.scss$/,
            use: [{
              loader: 'style-loader',
            }, {
              loader: 'css-loader',
            }, {
              loader: 'postcss-loader',
              options: {
                plugins: function () {
                  return [
                    //require('precss')
                    require('autoprefixer'),
                    require('cssnano')()
                  ];
                }
              }
            }, {
              loader: 'sass-loader'
            }
            ]
        }
      ],
    },
    plugins: [
      new webpack.DefinePlugin(envKeys),
    ]
  };
}
