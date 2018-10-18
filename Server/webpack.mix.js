let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .js('vendor/almasaeed2010/adminlte/bower_components/jquery/dist/jquery.min.js', 'public/js')
   .js('vendor/almasaeed2010/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js', 'public/js')
   .js('vendor/almasaeed2010/adminlte/dist/js/adminlte.min.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/admin.scss', 'public/css');

