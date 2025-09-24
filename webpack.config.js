const Path = require("path");
const PATHS = {
    MODULES: 'node_modules',
    FILES_PATH: '../',
    ROOT: Path.resolve(),
    SRC: Path.resolve('client/src'),
    DIST: Path.resolve('client/dist'),
};

const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'production',
    target: 'web',
    entry: {
        "vanilla-cookie-consent": [
            PATHS.SRC + '/javascript/vanilla-cookie-consent.js',
            PATHS.SRC + '/scss/vanilla-cookie-consent.scss'
        ],
        "vanilla-cookie-consent-dialog": [
            PATHS.SRC + '/javascript/vanilla-cookie-consent-dialog.js'
        ],
        "vanilla-cookie-consent-backend" : [
            PATHS.SRC + '/javascript/backend.js'
        ]
    },
    output: {
        filename: 'javascript/[name].min.js',
        path: PATHS.DIST,
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].min.css',
            chunkFilename: "[id].css"
        }),
    ],
    module: {
        rules: [
            {
                test: /\.(scss|css)$/,
                use: [
                    {
                        loader: 'style-loader'
                    },
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            esModule: false,
                        },
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    [
                                        "postcss-preset-env",
                                        {
                                            // Options
                                        },
                                    ],
                                ],
                            },
                        },
                    },
                    {
                        loader: 'resolve-url-loader',
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true
                        }
                    }
                ]
            },
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ['@babel/preset-env', { targets: "defaults" }]
                        ],
                    }
                }
            }
        ]
    }
}
