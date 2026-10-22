let mix = require('laravel-mix')
let path = require('path')

require('./nova.mix')

mix.setPublicPath('dist')
    .js('resources/js/tool.js', 'js')
    .sass('resources/sass/tool.sass', 'css', {
        sassOptions: {
            includePaths: [path.resolve(__dirname, 'node_modules')],
        },
    })
    .vue({ version: 3 })
    .nova('classic-o/nova-media-library')
