const mix = require('laravel-mix');

mix.webpackConfig({
    devServer: {
        port: 8081,
        allowedHosts: 'all',
        client: {
            webSocketURL: 'ws://localhost:8081/ws',
        },
        // إزالة السطر التالي (غير مدعوم في webpack-dev-server ≥ 4.x)
        // setupMiddlewares: (middlewares, devServer) => { ... },
        hot: true,
        open: true,
        compress: true,
    },
});

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css');
