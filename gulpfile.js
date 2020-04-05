const gulp = require('gulp');
const sass = require('gulp-sass');
const del = require('del');
const concat = require('gulp-concat');
const gulpif = require('gulp-if');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const sourcemaps = require('gulp-sourcemaps');
const zip = require('gulp-zip');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');

let prod = process.env.NODE_ENV === 'production';

const paths = {
    build: {
        js: 'assets/js/',
        css: 'assets/css/',
        fonts: 'assets/fonts/',
    },
    src: {
        js: 'src/js/',
        scss: 'src/scss/',
    },
    watch: {
        js: 'src/js/**/*',
        scss: 'src/scss/**/*'
    },
    clean: [
        'assets/js/**/*',
        'assets/css/**/*',
    ],
};

const postCssPlugins = [
    autoprefixer(),
];

if (prod) {
    postCssPlugins.push(cssnano());
}

gulp.task('clean', function () {
    return del(paths.clean);
});

gulp.task('scss:admin', function () {
    return gulp.src([
        paths.src.scss + 'admin.scss',
    ])
        .pipe(gulpif(!prod, sourcemaps.init()))
        .pipe(sass({
            noCache: true,
            style: 'compressed',
            includePaths: [
                'node_modules',
            ]
        }))
        .pipe(postcss(postCssPlugins))
        .pipe(concat('admin.min.css'))
        .pipe(gulpif(!prod, sourcemaps.write('.')))
        .pipe(gulp.dest(paths.build.css));
});

gulp.task('scss:styles', function () {
    return gulp.src([
        paths.src.scss + 'styles.scss',
    ])
        .pipe(gulpif(!prod, sourcemaps.init()))
        .pipe(sass({
            noCache: true,
            style: 'compressed',
            includePaths: [
                'node_modules',
            ]
        }))
        .pipe(postcss(postCssPlugins))
        .pipe(concat('styles.min.css'))
        .pipe(gulpif(!prod, sourcemaps.write('.')))
        .pipe(gulp.dest(paths.build.css));
});

const jsUglifyCondition = function (file) {
    if (!prod) {
        return false;
    }

    if (file.path.match(/node_modules/g)) {
        return false;
    }

    return true;
};


gulp.task('js', function () {
    return gulp.src([
        paths.src.js + 'scripts.js',
    ])
        .pipe(gulpif(!prod, sourcemaps.init()))
        .pipe(babel())
        .pipe(gulpif(jsUglifyCondition, uglify({mangle: false})))
        .pipe(concat('scripts.min.js'))
        .pipe(gulpif(!prod, sourcemaps.write('.')))
        .pipe(gulp.dest(paths.build.js));
});

gulp.task('scss', gulp.parallel('scss:admin', 'scss:styles'));
gulp.task('js', gulp.parallel('js'));

gulp.task('watch', gulp.parallel(function () {
    gulp.watch(paths.watch.scss, gulp.parallel('scss'));
    gulp.watch(paths.watch.js, gulp.parallel('js'));
}));

gulp.task('zip', function () {
    return gulp.src([
        './assets/**/*',
        '!./assets/manifest/',
        '!./assets/manifest/**/*',
        './lang/**/*.php',
        './updates/**/*',
        './Plugin.php',
        './plugin.yaml',
    ], {base: '.'})
        .pipe(zip('blog.zip'))
        .pipe(gulp.dest('.'));
});

gulp.task('build', gulp.series('clean', gulp.parallel('scss', 'js'), 'zip'));

gulp.task('default', gulp.series('build'));
