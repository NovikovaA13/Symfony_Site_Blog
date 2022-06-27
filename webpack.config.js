var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .addEntry('js/app', ['./assets/js/app.js', './node_modules/bootstrap/dist/js/bootstrap.min.js', './node_modules/@fortawesome/fontawesome-free/js/all.js'])
    .addStyleEntry('css/app', ['./node_modules/bootstrap/dist/css/bootstrap.min.css', './node_modules/@fortawesome/fontawesome-free/css/all.min.css', './assets/styles/app.css']);

module.exports = Encore.getWebpackConfig();
