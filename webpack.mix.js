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

mix.copyDirectory('resources/assets/img', 'public/assets/img');
mix.copyDirectory('resources/assets/images', 'public/assets/images');
mix.copyDirectory('resources/assets/icons', 'public/assets/icons');
mix.copyDirectory('node_modules/summernote/dist/font',
    'public/assets/css/font');
mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts',
    'public/assets/webfonts');

mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css',
    'public/assets/css/bootstrap.min.css');
mix.copy('node_modules/izitoast/dist/css/iziToast.min.css',
    'public/assets/css/iziToast.min.css');
mix.copy('node_modules/datatables.net-dt/css/jquery.dataTables.min.css',
    'public/assets/css/jquery.dataTables.min.css');
mix.copy('node_modules/datatables.net-dt/images', 'public/assets/images');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.min.css',
    'public/assets/css/font-awesome.min.css');
mix.copy('node_modules/sweetalert/dist/sweetalert.css',
    'public/assets/css/sweetalert.css');
mix.copy('node_modules/select2/dist/css/select2.min.css',
    'public/assets/css/select2.min.css');
mix.copy('node_modules/summernote/dist/summernote.min.css',
    'public/assets/css/summernote.min.css');
mix.babel('node_modules/chart.js/dist/Chart.min.css',
    'public/assets/css/Chart.min.css');
mix.babel('node_modules/@simonwep/pickr/dist/themes/nano.min.css',
    'public/assets/css/nano.min.css');
mix.copy('node_modules/daterangepicker/daterangepicker.css',
    'public/assets/css/daterangepicker.css');

/* css */
mix.sass('resources/assets/sass/custom.scss', 'public/assets/css/custom.css').
    sass('resources/assets/sass/admin_theme.scss',
        'public/assets/css/admin_theme.css').
    sass('resources/assets/sass/infy-loader.scss',
        'public/assets/css/infy-loader.css').
    sass('resources/assets/sass/dashboard-widgets.scss',
        'public/assets/css/dashboard-widgets.css').
    sass('resources/assets/sass/web.scss',
        'public/assets/css/web.css').
    sass('resources/assets/sass/landing-page-style.scss',
        'public/assets/css/landing-page-style.css').
    sass('resources/assets/sass/ticket.scss',
        'public/assets/css/ticket.css').
    sass('resources/assets/sass/new-conversation.scss',
        'public/assets/css').
    sass('resources/assets/sass/chat.scss',
        'public/assets/css/chat.css').
    sass('resources/assets/sass/web_theme.scss',
        'public/assets/css/web_theme.css').
    sass('resources/assets/sass/web_theme_components.scss',
        'public/assets/css/web_theme_components.css').
    sass('resources/assets/sass/web_chat.scss',
        'public/assets/css/web_chat.css').
    sass('resources/assets/sass/category.scss',
        'public/assets/css/category.css').
    sass('resources/assets/sass/faqs.scss',
        'public/assets/css/faqs.css').
    sass('resources/assets/sass/users.scss',
        'public/assets/css/users.css').
    sass('resources/assets/sass/customer.scss',
        'public/assets/css/customer.css').
    sass('resources/assets/sass/customer_ticket.scss',
        'public/assets/css/customer_ticket.css').
    sass('resources/assets/sass/404_error_page.scss',
        'public/assets/css/404_error_page.css').
    sass('resources/assets/sass/phone-number-code.scss',
        'public/assets/css/phone-number-code.css').
    sass('resources/assets/sass/error-pages/minimal-error-page.scss',
        'public/assets/css/error-pages/minimal-error-page.css').
    sass('resources/assets/sass/error-pages/illustrated-error.scss',
        'public/assets/css/error-pages/illustrated-error.css').
    sass('resources/assets/sass/error-pages/layout.scss',
        'public/assets/css/error-pages/layout.css').
    version();


mix.copy('node_modules/video.js/dist/video-js.css',
    'public/assets/css/video-js.css');
mix.copy('node_modules/@coreui/coreui/dist/css/coreui.min.css',
    'public/assets/css/coreui.min.css');
mix.copy('node_modules/simple-line-icons/css/simple-line-icons.css',
    'public/assets/css/simple-line-icons.css');

mix.babel('node_modules/bootstrap/dist/js/bootstrap.min.js',
    'public/assets/js/bootstrap.min.js');
mix.babel('node_modules/moment/min/moment.min.js',
    'public/assets/js/moment.min.js');
mix.babel('node_modules/jquery/dist/jquery.min.js',
    'public/assets/js/jquery.min.js');
mix.babel('node_modules/popper.js/dist/umd/popper.min.js',
    'public/assets/js/popper.min.js');
mix.babel('node_modules/moment/min/moment.min.js',
    'public/assets/js/moment.min.js');
mix.babel('node_modules/izitoast/dist/js/iziToast.min.js',
    'public/assets/js/iziToast.min.js');
mix.babel('node_modules/datatables.net/js/jquery.dataTables.min.js',
    'public/assets/js/jquery.dataTables.min.js');
mix.babel('node_modules/jquery.nicescroll/dist/jquery.nicescroll.js',
    'public/assets/js/jquery.nicescroll.js');
mix.babel('node_modules/select2/dist/js/select2.min.js',
    'public/assets/js/select2.min.js');
mix.babel('node_modules/sweetalert/dist/sweetalert.min.js',
    'public/assets/js/sweetalert.min.js');
mix.babel('node_modules/summernote/dist/summernote.min.js',
    'public/assets/js/summernote.min.js');
mix.babel('node_modules/chart.js/dist/Chart.js',
    'public/assets/js/Chart.js');
mix.babel('node_modules/@simonwep/pickr/dist/pickr.min.js',
    'public/assets/js/pickr.min.js');
mix.babel('node_modules/daterangepicker/daterangepicker.js',
    'public/assets/js/daterangepicker.js');
mix.babel('node_modules/jsrender/jsrender.js',
    'public/assets/js/jsrender.js');

mix.copy('node_modules/sweetalert2/dist/sweetalert2.all.min.js',
    'public/assets/js/sweetalert2.all.min.js');
mix.copy('node_modules/video.js/dist/video.min.js',
    'public/assets/js/video.min.js');
mix.copy('node_modules/@coreui/coreui/dist/js/coreui.min.js',
    'public/assets/js/coreui.min.js');
mix.copy('node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js',
    'public/assets/js/perfect-scrollbar.min.js');
mix.copy('node_modules/emojione/lib/js/emojione.min.js',
    'public/assets/js/emojione.min.js');
mix.copy('node_modules/icheck/', 'public/assets/icheck/');

mix.js('resources/assets/js/app.js', 'public/assets/js').
    js('resources/assets/js/chat.js', 'public/assets/js').
    js('resources/assets/js/notification.js', 'public/assets/js').
    js('resources/assets/js/set-user-on-off.js', 'public/assets/js').
    js('resources/assets/js/web/set-user-on-off.js', 'public/assets/js/web/').
    js('resources/assets/js/custom/custom.js',
        'public/assets/js/custom/custom.js').
    js('resources/assets/js/custom/custom-datatable.js',
        'public/assets/js/custom/custom-datatable.js').
    js('resources/assets/js/custom/input_price_format.js',
        'public/assets/js/custom/input_price_format.js').
    js('resources/assets/js/custom/currency.js',
        'public/assets/js/custom/currency.js').
    js('resources/assets/js/user_profile/user_profile.js',
        'public/assets/js/user_profile/user_profile.js').
    js('resources/assets/js/users/users.js',
        'public/assets/js/users/users.js').
    js('resources/assets/js/users/admins.js',
        'public/assets/js/users/admins.js').
    js('resources/assets/js/users/create_edit.js',
        'public/assets/js/users/create_edit.js').
    js('resources/assets/js/categories/categories.js',
        'public/assets/js/categories/categories.js').
    js('resources/assets/js/categories/show_category.js',
        'public/assets/js/categories/show_category.js').
    js('resources/assets/js/tickets/tickets.js',
        'public/assets/js/tickets/tickets.js').
    js('resources/assets/js/tickets/create_edit.js',
        'public/assets/js/tickets/create_edit.js').
    js('resources/assets/js/tickets/view_tickets.js',
        'public/assets/js/tickets/view_tickets.js').
    js('resources/assets/js/faqs/faqs.js',
        'public/assets/js/faqs/faqs.js').
    js('resources/assets/js/settings/settings.js',
        'public/assets/js/settings/settings.js').
    js('resources/assets/js/dashboard/dashboard.js',
        'public/assets/js/dashboard/dashboard.js').
    js('resources/assets/js/web/chat.js',
        'public/assets/js/web/chat.js').
    js('resources/assets/js/user_ticket/user_ticket.js',
        'public/assets/js/user_ticket/user_ticket.js').
    js('resources/assets/js/web/tickets.js',
        'public/assets/js/web/tickets.js').
    js('resources/assets/js/web/public-tickets-autocomplete.js',
        'public/assets/js/web/public-tickets-autocomplete.js').
    js('resources/assets/js/customer_dashboard/tickets.js',
        'public/assets/js/customer_dashboard/tickets.js').
    js('resources/assets/js/custom/phone-number-code.js',
        'public/assets/js/custom/phone-number-code.js').
    js('resources/assets/js/language_translate/language_translate.js',
        'public/assets/js/language_translate/language_translate.js').
    version();
