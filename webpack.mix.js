const mix = require('laravel-mix');

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

// mix.js('resources/js/app.js', 'public/js')
   mix.sass('resources/sass/app.scss', 'public/css');

// mix.js('resources/js/pages/RatingAndReview.js', 'public/js/pages/RatingAndReview.js')
// mix.js('resources/js/pages/MenuDetails.js', 'public/js/pages/MenuDetails.js')
// mix.js('resources/js/pages/MenuDetailsView.js', 'public/js/pages/MenuDetailsView.js')
// mix.js('resources/js/pages/Passport.js', 'public/js/pages/Passport.js')
    // .sass('resources/sass/app.scss', 'public/css');
