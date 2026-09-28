const mix = require('laravel-mix');
const lodash = require("lodash");
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const folder = {
    src: "resources/", // source files
    dist: "public/", // build files
    dist_assets: "public/build/" //build assets files
};

mix.js('resources/js/vue.js', 'public/vue/js')
    .js('resources/js/vueWidgets.js', 'public/vue/js')
    .js('resources/js/quiz.js', 'public/vue/js')
    .js('resources/js/dashboard.js', 'public/vue/js')
    .js('resources/js/agentDashboard.js', 'public/vue/js')
    .js('resources/js/kpi.js', 'public/vue/js')
    .js('resources/js/match-listings.js', 'public/vue/js')
    .js('resources/js/matching.js', 'public/vue/js')
    .js('resources/js/search-listings.js', 'public/vue/js')
    .js('resources/js/admin-dashboard.js', 'public/vue/js')
    .js('resources/js/listing-verifications.js', 'public/vue/js')
    .js('resources/js/pipeline.js', 'public/vue/js')
    .js('resources/js/consolidatedReport.js', 'public/vue/js')
    .vue()
    .postCss('resources/css/app.css', 'public/vue/css', [
        //
    ]).copy('node_modules/flatpickr/dist/flatpickr.min.js', 'public/vue/js')
    .copy('node_modules/flatpickr/dist/flatpickr.min.css', 'public/vue/css');/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// var third_party_assets = {
//     css_js: [
//         { "name": "@ckeditor", "assets": ["./node_modules/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"] },
//         {
//             "name": "@simonwep",
//             "assets": ["./node_modules/@simonwep/pickr/dist/pickr.min.js",
//                 "./node_modules/@simonwep/pickr/dist/themes/classic.min.css",
//                 "./node_modules/@simonwep/pickr/dist/themes/monolith.min.css",
//                 "./node_modules/@simonwep/pickr/dist/themes/nano.min.css",
//             ]
//         },
//         { "name": "bootstrap", "assets": ["./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"] },
//         {
//             "name": "@tarekraafat",
//             "assets": [
//                 "./node_modules/@tarekraafat/autocomplete.js/dist/autoComplete.min.js",
//                 "./node_modules/@tarekraafat/autocomplete.js/dist/css/autoComplete.css",
//             ]
//         },
//         {
//             "name": "aos",
//             "assets": [
//                 "./node_modules/aos/dist/aos.js",
//                 "./node_modules/aos/dist/aos.css",
//             ]
//         },
//
//         { "name": "dom-autoscroller", "assets": ["./node_modules/dom-autoscroller/dist/dom-autoscroller.min.js"] },
//         {
//             "name": "dragula",
//             "assets": ["./node_modules/dragula/dist/dragula.min.js",
//                 "./node_modules/dragula/dist/dragula.min.css"
//             ]
//         },
//         {
//             "name": "choices.js",
//             "assets": ["./node_modules/choices.js/public/assets/scripts/choices.min.js",
//                 "./node_modules/choices.js/public/assets/styles/choices.min.css"
//             ]
//         },
//         { "name": "cleave.js", "assets": ["./node_modules/cleave.js/dist/cleave.min.js"] },
//         { "name": "apexcharts", "assets": ["./node_modules/apexcharts/dist/apexcharts.min.js"] },
//         // { "name": "bootstrap", "assets": ["./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"] },
//         { "name": "chart.js", "assets": ["./node_modules/chart.js/dist/chart.min.js"] },
//         { "name": "fg-emoji-picker", "assets": ["./node_modules/fg-emoji-picker/fgEmojiPicker.js"] },
//
//         {
//             "name": "filepond-plugin-file-encode",
//             "assets": ["./node_modules/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js", ]
//         },
//         {
//             "name": "filepond-plugin-file-validate-size",
//             "assets": ["./node_modules/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js", ]
//         },
//         {
//             "name": "filepond-plugin-image-exif-orientation",
//             "assets": ["./node_modules/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js", ]
//         },
//         {
//             "name": "filepond-plugin-image-exif-orientation",
//             "assets": ["./node_modules/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js", ]
//         },
//         {
//             "name": "filepond-plugin-image-preview",
//             "assets": [
//                 "./node_modules/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js",
//                 "./node_modules/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css",
//             ]
//         },
//         {
//             "name": "filepond",
//             "assets": [
//                 "./node_modules/filepond/dist/filepond.min.js",
//                 "./node_modules/filepond/dist/filepond.min.css",
//             ]
//         },
//         {
//             "name": "dropzone",
//             "assets": ["./node_modules/dropzone/dist/dropzone-min.js",
//                 "./node_modules/dropzone/dist/dropzone.css"
//             ]
//         },
//         { "name": "echarts", "assets": ["./node_modules/echarts/dist/echarts.min.js"] },
//
//         {
//             "name": "fullcalendar",
//             "assets": [
//                 "./node_modules/fullcalendar/main.min.js",
//                 "./node_modules/fullcalendar/main.min.css"
//             ]
//         },
//         {
//             "name": "flatpickr",
//             "assets": ["./node_modules/flatpickr/dist/flatpickr.min.js",
//                 "./node_modules/flatpickr/dist/flatpickr.min.css"
//             ]
//         },
//         {
//             "name": "glightbox",
//             "assets": ["./node_modules/glightbox/dist/js/glightbox.min.js",
//                 "./node_modules/glightbox/dist/css/glightbox.min.css"
//             ]
//         },
//         { "name": "gmaps", "assets": ["./node_modules/gmaps/gmaps.min.js"] },
//
//         { "name": "isotope-layout", "assets": ["./node_modules/isotope-layout/dist/isotope.pkgd.min.js"] },
//
//         {
//             "name": "gridjs",
//             "assets": ["./node_modules/gridjs/dist/gridjs.umd.js",
//                 "./node_modules/gridjs/dist/theme/mermaid.min.css"
//             ]
//         },
//         // {
//         //     "name": "jsvectormap",
//         //     "assets": [
//         //         "./node_modules/jsvectormap/dist/css/jsvectormap.min.css",
//         //         "./node_modules/jsvectormap/dist/js/jsvectormap.min.js",
//         //         "./node_modules/jsvectormap/dist/maps/world-merc.js",
//         //         "./node_modules/jsvectormap/dist/maps/canada.js",
//         //         "./node_modules/jsvectormap/dist/maps/russia.js",
//         //         "./node_modules/jsvectormap/dist/maps/spain.js",
//         //     ]
//         // },
//         {
//             "name": "leaflet",
//             "assets": [
//                 "./node_modules/leaflet/dist/leaflet.js",
//                 "./node_modules/leaflet/dist/leaflet.css",
//             ]
//         },
//         { "name": "masonry-layout", "assets": ["./node_modules/masonry-layout/dist/masonry.pkgd.min.js"] },
//         { "name": "particles.js", "assets": ["./node_modules/particles.js/particles.js"] },
//
//         {
//             "name": "prismjs",
//             "assets": [
//                 "./node_modules/prismjs/prism.js",
//                 "./node_modules/prismjs/themes/prism.css",
//                 "./node_modules/prismjs/plugins/toolbar/prism-toolbar.min.css",
//
//             ]
//         },
//
//         { "name": "list.pagination.js", "assets": ["./node_modules/list.pagination.js/dist/list.pagination.min.js"] },
//
//         { "name": "list.js", "assets": ["./node_modules/list.js/dist/list.min.js"] },
//         {
//             "name": "multi.js",
//             "assets": [
//                 "./node_modules/multi.js/dist/multi.min.js",
//                 "./node_modules/multi.js/dist/multi.min.css",
//             ]
//         },
//
//         { "name": "moment", "assets": ["./node_modules/moment/min/moment.min.js"] },
//         {
//             "name": "nouislider",
//             "assets": ["./node_modules/nouislider/dist/nouislider.min.js",
//                 "./node_modules/nouislider/dist/nouislider.min.css"
//             ]
//         },
//         {
//             "name": "quill",
//             "assets": [
//                 "./node_modules/quill/dist/quill.core.css",
//                 "./node_modules/quill/dist/quill.bubble.css",
//                 "./node_modules/quill/dist/quill.snow.css",
//                 "./node_modules/quill/dist/quill.min.js"
//             ]
//         },
//         { "name": "rater-js", "assets": ["./node_modules/rater-js/index.js"] },
//
//         {
//             "name": "shepherd.js",
//             "assets": [
//                 "./node_modules/shepherd.js/dist/js/shepherd.min.js",
//                 "./node_modules/shepherd.js/dist/css/shepherd.css",
//             ]
//         },
//
//
//         { "name": "simplebar", "assets": ["./node_modules/simplebar/dist/simplebar.min.js"] },
//         {
//             "name": "sweetalert2",
//             "assets": ["./node_modules/sweetalert2/dist/sweetalert2.min.js",
//                 "./node_modules/sweetalert2/dist/sweetalert2.min.css"
//             ]
//         },
//         {
//             "name": "swiper",
//             "assets": ["./node_modules/swiper/swiper-bundle.min.js",
//                 "./node_modules/swiper/swiper-bundle.min.css"
//             ]
//         },
//
//         { "name": "feather-icons", "assets": ["./node_modules/feather-icons/dist/feather.min.js"] },
//         { "name": "wnumb", "assets": ["./node_modules/wnumb/wNumb.min.js"] },
//         { "name": "node-waves", "assets": ["./node_modules/node-waves/dist/waves.min.js"] },
//         { "name": "sortablejs", "assets": ["./node_modules/sortablejs/Sortable.min.js"] }
//     ]
// };
//
// //copying third party assets
// lodash(third_party_assets).forEach(function(assets, type) {
//     if (type == "css_js") {
//         lodash(assets).forEach(function(plugin) {
//             var name = plugin['name'],
//                 assetlist = plugin['assets'],
//                 css = [],
//                 js = [];
//             lodash(assetlist).forEach(function(asset) {
//                 var ass = asset.split(',');
//                 for (let i = 0; i < ass.length; ++i) {
//                     if (ass[i].substr(ass[i].length - 3) == ".js") {
//                         js.push(ass[i]);
//                     } else {
//                         css.push(ass[i]);
//                     }
//                 };
//             });
//             if (js.length > 0) {
//                 mix.combine(js, folder.dist_assets + "/libs/" + name + "/" + name + ".min.js");
//             }
//             if (css.length > 0) {
//                 mix.combine(css, folder.dist_assets + "/libs/" + name + "/" + name + ".min.css");
//             }
//         });
//     }
// });

// // mix.copyDirectory("./node_modules/tinymce", folder.dist_assets + "/libs/tinymce");
// mix.copyDirectory("./node_modules/leaflet/dist/images", folder.dist_assets + "/libs/leaflet/images");
// // mix.copyDirectory("./node_modules/bootstrap-editable/img", folder.dist_assets + "/libs/img");
//
// // copy all fonts
// var out = folder.dist_assets + "fonts";
// mix.copyDirectory(folder.src + "fonts", out);
//
// // copy all images
// var out = folder.dist_assets + "images";
// mix.copyDirectory(folder.src + "images", out);
//
// //copy all json
// var out = folder.dist_assets + "json";
// mix.copyDirectory(folder.src + "json", out);

// mix.sass('resources/scss/bootstrap.scss', folder.dist_assets + "css").minify(folder.dist_assets + "css/bootstrap.css");
// mix.sass('resources/scss/icons.scss', folder.dist_assets + "css").options({ processCssUrls: false }).minify(folder.dist_assets + "css/icons.css");
// mix.sass('resources/scss/app.scss', folder.dist_assets + "css").options({ processCssUrls: false }).minify(folder.dist_assets + "css/app.css");
// mix.sass('resources/scss/custom.scss', folder.dist_assets + "css").options({ processCssUrls: false }).minify(folder.dist_assets + "css/custom.css");


mix.webpackConfig({
    plugins: [
        new WebpackRTLPlugin()
    ]
});


//copying demo velzon related assets
// var app_velzon_assets = {
//     js: [
//         folder.src + "js/velzon/animation-aos.init.js",
//         folder.src + "js/velzon/apexcharts-area.init.js",
//         folder.src + "js/velzon/apexcharts-bar.init.js",
//         folder.src + "js/velzon/apexcharts-boxplot.init.js",
//         folder.src + "js/velzon/apexcharts-bubble.init.js",
//         folder.src + "js/velzon/apexcharts-candlestick.init.js",
//         folder.src + "js/velzon/apexcharts-column.init.js",
//         folder.src + "js/velzon/apexcharts-heatmap.init.js",
//         folder.src + "js/velzon/apexcharts-line.init.js",
//         folder.src + "js/velzon/apexcharts-mixed.init.js",
//         folder.src + "js/velzon/apexcharts-pie.init.js",
//         folder.src + "js/velzon/apexcharts-polararea.init.js",
//         folder.src + "js/velzon/apexcharts-radar.init.js",
//         folder.src + "js/velzon/apexcharts-radialbar.init.js",
//         folder.src + "js/velzon/apexcharts-scatter.init.js",
//         folder.src + "js/velzon/apexcharts-timeline.init.js",
//         folder.src + "js/velzon/apexcharts-treemap.init.js",
//         folder.src + "js/velzon/apps-nft-auction.init.js",
//         folder.src + "js/velzon/apps-nft-create.init.js",
//         folder.src + "js/velzon/apps-nft-explore.init.js",
//         folder.src + "js/velzon/apps-nft-item-details.init.js",
//         folder.src + "js/velzon/apps-nft-ranking.init.js",
//         folder.src + "js/velzon/card.init.js",
//         folder.src + "js/velzon/chartjs.init.js",
//         folder.src + "js/velzon/chat.init.js",
//         folder.src + "js/velzon/coming-soon.init.js",
//         folder.src + "js/velzon/crm-deals.init.js",
//         folder.src + "js/velzon/crypto-buy-sell.init.js",
//         folder.src + "js/velzon/crypto-kyc.init.js",
//         folder.src + "js/velzon/crypto-orders.init.js",
//         folder.src + "js/velzon/crypto-transactions.init.js",
//         folder.src + "js/velzon/crypto-wallet.init.js",
//         folder.src + "js/velzon/dashboard-analytics.init.js",
//         folder.src + "js/velzon/dashboard-crm.init.js",
//         folder.src + "js/velzon/dashboard-crypto.init.js",
//         folder.src + "js/velzon/dashboard-ecommerce.init.js",
//         folder.src + "js/velzon/dashboard-projects.init.js",
//         folder.src + "js/velzon/dashboard-nft.init.js",
//         folder.src + "js/velzon/datatables.init.js",
//         folder.src + "js/velzon/echarts.init.js",
//         // folder.src + "js/velzon/ecommerce-cart.init.js",
//         // folder.src + "js/velzon/ecommerce-customer-list.init.js",
//         folder.src + "js/velzon/ecommerce-product-checkout.init.js",
//         folder.src + "js/velzon/ecommerce-product-create.init.js",
//         folder.src + "js/velzon/ecommerce-product-details.init.js",
//         folder.src + "js/velzon/ecommerce-product-list.init.js",
//         folder.src + "js/velzon/form-file-upload.init.js",
//         folder.src + "js/velzon/form-input-spin.init.js",
//         folder.src + "js/velzon/form-advanced.init.js",
//         folder.src + "js/velzon/form-editor.init.js",
//         folder.src + "js/velzon/form-masks.init.js",
//         folder.src + "js/velzon/form-pickers.init.js",
//         folder.src + "js/velzon/form-validation.init.js",
//         folder.src + "js/velzon/form-wizard.init.js",
//         folder.src + "js/velzon/gallery.init.js",
//         folder.src + "js/velzon/gmaps.init.js",
//         folder.src + "js/velzon/gridjs.init.js",
//         //folder.src + "js/velzon/invoicecreate.init.js",
//         // folder.src + "js/velzon/invoiceslist.init.js",
//         folder.src + "js/velzon/leaflet-map.init.js",
//         folder.src + "js/velzon/leaflet-us-states.js",
//         //folder.src + "js/velzon/listjs.init.js",
//         folder.src + "js/velzon/mailbox.init.js",
//         folder.src + "js/velzon/modal.init.js",
//         folder.src + "js/velzon/materialdesign.list.js",
//         folder.src + "js/velzon/notifications.init.js",
//         folder.src + "js/velzon/nestable.init.js",
//         folder.src + "js/velzon/nft-landing.init.js",
//         folder.src + "js/velzon/particles.app.js",
//         folder.src + "js/velzon/password-addon.init.js",
//         folder.src + "js/velzon/passowrd-create.init.js",
//         folder.src + "js/velzon/pricing.init.js",
//         folder.src + "js/velzon/profile-setting.init.js",
//         folder.src + "js/velzon/profile.init.js",
//         folder.src + "js/velzon/project-create.init.js",
//         folder.src + "js/velzon/project-list.init.js",
//         folder.src + "js/velzon/project-overview.init.js",
//         folder.src + "js/velzon/range-sliders.init.js",
//         folder.src + "js/velzon/rating.init.js",
//         folder.src + "js/velzon/remix-icons-listing.js",
//         folder.src + "js/velzon/search-result.init.js",
//         folder.src + "js/velzon/seller-details.init.js",
//         folder.src + "js/velzon/sellers.init.js",
//         folder.src + "js/velzon/select2.init.js",
//         folder.src + "js/velzon/sweetalerts.init.js",
//         folder.src + "js/velzon/swiper.init.js",
//         folder.src + "js/velzon/tasks-kanban.init.js",
//         // folder.src + "js/velzon/tasks-list.init.js",
//         folder.src + "js/velzon/team.init.js",
//         folder.src + "js/velzon/ticketdetail.init.js",
//         // folder.src + "js/velzon/ticketlist.init.js",
//         folder.src + "js/velzon/timeline.init.js",
//         folder.src + "js/velzon/tour.init.js",
//         folder.src + "js/velzon/two-step-verification.init.js",
//         folder.src + "js/velzon/vector-maps.init.js",
//         folder.src + "js/velzon/landing.init.js",
//         folder.src + "js/velzon/widgets.init.js"
//     ]
// };

// var out = folder.dist_assets + "js/";
// lodash(app_velzon_assets).forEach(function(assets, type) {
//     for (let i = 0; i < assets.length; ++i) {
//         mix.js(assets[i], out + "velzon");
//     };
// });

// mix.combine('resources/js/plugins.js', folder.dist_assets + "js/plugins.min.js");
// mix.combine('resources/js/layout.js', folder.dist_assets + "js/layout.js");
// mix.combine('resources/js/bootstrap.js', folder.dist_assets + "js/bootstrap.min.js");
// mix.combine('resources/js/app.js', folder.dist_assets + "js/app.min.js");
// mix.combine('resources/js/velzon/leaflet-us-states.js', folder.dist_assets + "js/velzon/leaflet-us-states.js");
// mix.combine('resources/js/velzon/form-wizard.init.js', folder.dist_assets + "js/velzon/form-wizard.init.js");
// mix.combine('resources/js/velzon/crm-leads.init.js', folder.dist_assets + "js/velzon/crm-leads.init.js");
// mix.combine('resources/js/velzon/crm-companies.init.js', folder.dist_assets + "js/velzon/crm-companies.init.js");
// mix.combine('resources/js/velzon/crm-contact.init.js', folder.dist_assets + "js/velzon/crm-contact.init.js");
// mix.combine('resources/js/velzon/ecommerce-order.init.js', folder.dist_assets + "js/velzon/ecommerce-order.init.js");
// mix.combine('resources/js/velzon/ecommerce-customer-list.init.js', folder.dist_assets + "js/velzon/ecommerce-customer-list.init.js");
// mix.combine('resources/js/velzon/ecommerce-cart.init.js', folder.dist_assets + "js/velzon/ecommerce-cart.init.js");
// mix.combine('resources/js/velzon/tasks-list.init.js', folder.dist_assets + "js/velzon/tasks-list.init.js");
// mix.combine('resources/js/velzon/invoicecreate.init.js', folder.dist_assets + "js/velzon/invoicecreate.init.js");
// mix.combine('resources/js/velzon/invoicedetails.js', folder.dist_assets + "js/velzon/invoicedetails.js");
// mix.combine('resources/js/velzon/invoiceslist.init.js', folder.dist_assets + "js/velzon/invoiceslist.init.js");
// mix.combine('resources/js/velzon/ticketlist.init.js', folder.dist_assets + "js/velzon/ticketlist.init.js");
// mix.combine('resources/js/velzon/listjs.init.js', folder.dist_assets + "js/velzon/listjs.init.js");
// mix.combine('resources/js/velzon/calendar.init.js', folder.dist_assets + "js/velzon/calendar.init.js");
// mix.combine('resources/js/velzon/plugins/lord-icon-2.1.0.js', folder.dist_assets + "js/velzon/plugins/lord-icon-2.1.0.min.js");
// mix.combine('resources/js/velzon/plugins/jsonfull-emoji-list.json', folder.dist_assets + "js/velzon/plugins/jsonfull-emoji-list.json");
// mix.combine('resources/js/velzon/plugins/event.init.json', folder.dist_assets + "js/velzon/plugins/event.init.json");
