/* import necessary npm packages */
const gulp = require('gulp');
const uglify = require('gulp-uglify-es').default;
const cleancss = require('gulp-clean-css');
const concat = require('gulp-concat');
const browserSync = require('browser-sync').create();
const gulpInject = require('gulp-inject');
const series = require('stream-series');
const merge = require('merge-stream');
const gulpfilter = require('gulp-filter');


// Assets sources
const vendor = './src/assets/vendor_assets';
const theme = './src/assets/theme_assets';
const vendorAssets = gulp.src(
        [       `${vendor}/css/tailwind/*.css`,
                `${vendor}/css/*.css`,
                `${vendor}/js/*.js`,
        ],
        { read: true }
);
const themeAssets = gulp.src(['src/tailwind.css', `${theme}/js/*.js`], { read: true });


/* gulp asset injection */
gulp.task('inject', function (done) {
    gulp.src('./src/*.html')
        .pipe(gulpInject(series(vendorAssets, themeAssets), { relative: true }))
        .pipe(gulp.dest('./src/'))
    done();
});

/* gulp serve content in browser */
gulp.task('serve', function(done) {
    browserSync.init({
        server: {
            baseDir: './src'
        },
        port: 3010
    })
    done();
});

// default gulp task
gulp.task('default', gulp.series('inject', 'serve', function () {
    gulp.watch('./src/**/*.js', browserSync.reload);
}));

// gulp build task: generate an upladable version of the template
var filesToMove = [
    vendor+'/**',
    '!src/assets/vendor_assets/css/**/!(*.css)',
    theme+'/**',
    '!src/assets/theme_assets/{sass,sass/**}',
    './src/*.html', './src/*.css',
    './src/images/**',
    './src/data/**'
];

gulp.task('move', function(done){
    gulp.src(filesToMove, {base: './src'})
        .pipe(gulp.dest('build'));
    done();
});

gulp.task('build', gulp.series('move'));

// eject optimized  version for demo
gulp.task('distAssets', function (done) {
    var jsFilter = gulpfilter(['**/*.js'], {restore: true}),
        cssFilter = gulpfilter(['**/*css'], {restore: true}),
        thmis = gulpfilter(['**/*.js'], {restore: true});

    var va = vendorAssets
        .pipe(jsFilter)
        .pipe(uglify())
        .on('error', function (e) {
            console.log(e);
        })
        .pipe(concat('plugins.min.js'))
        .pipe(gulp.dest('dist/js'))
        .pipe(jsFilter.restore)
        .pipe(cssFilter)
        .pipe(cleancss(
            {
                compatibility: 'ie8',
                rebase: false
            }))
        .pipe(concat('plugin.min.css'))
        .pipe(gulp.dest('./dist/css'));

    var ta = themeAssets
        .pipe(thmis)
        .pipe(uglify())
        .on('error', function (e) {
            console.log(e);
        })
        .pipe(concat('script.min.js'))
        .pipe(gulp.dest('dist/js'))
        .pipe(thmis.restore)
        .pipe(gulpfilter(['**/*.css']))
        .pipe(cleancss({compatibility: 'ie8'}))

    const tailwind = gulp.src('./src/*.css').pipe(gulp.dest('dist'));
    var fonts = gulp.src('./src/assets/vendor_assets/fonts/**')
        .pipe(gulp.dest('dist/fonts'));

    var moveHtml = gulp.src('src/*.html')
        .pipe(gulp.dest('dist'));

    return merge(va, ta,tailwind, fonts, moveHtml);
    done();
});

//'imgoptimize'
gulp.task('build:optimize', gulp.series('distAssets', function (done) {
    gulp.src('dist/*.html')
        .pipe(gulpInject(
            gulp.src(['dist/css/*.css', 'dist/js/*.js', 'dist/*.css' ]),
            {relative: true}
        ))
        .pipe(gulp.dest('dist'));
    
        gulp.src('src/images/**')
            .pipe(gulp.dest('dist/images'));

            gulp.src('src/data/**')
            .pipe(gulp.dest('dist/data'));
    done();
}));
