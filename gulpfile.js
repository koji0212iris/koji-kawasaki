var gulp            = require('gulp');
var sass            = require('gulp-sass'); //<-
var pleeease        = require('gulp-pleeease');
var plumber         = require('gulp-plumber');
var notify          = require('gulp-notify');
var cached          = require('gulp-cached');
var svgmin          = require('gulp-svgmin');
var browserSync     = require('browser-sync');
var sftp            = require('gulp-sftp');

var config = require('./config');
// var ftp = require('./ftp');
var path = require('./setpath');

gulp.task('sass', function() {
	return gulp.src('./src/sass/**/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(cached())
		.pipe(plumber({
			errorHandler: notify.onError("Error: <%= error.message %>") //<-
		}))
		.pipe(pleeease({
			autoprefixer: {"browsers": ["last 4 versions", "ios 6"]}, //ベンダープレフィックス
			mqpacker: true,
			minifier: true, //圧縮の有無 true/false
			rem: ["10px"]
		}))
		// .pipe(sass({outputStyle: 'compressed'}))
		.pipe(gulp.dest('./public_html/'))
		// .pipe(sftp({
		// 	host: ftp.host,
		// 	user: ftp.auth,
		// 	pass: ftp.pass,
		// 	remotePath: ftp.remotePath
		// }))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('ftp_upload', function() {
	gulp.src([path.html, path.js])
	.pipe(cached())
	// .pipe(sftp({
	// 	host: ftp.host,
	// 	user: ftp.auth,
	// 	pass: ftp.pass,
	// 	remotePath: path.remotePath
	// }))
	.pipe(browserSync.reload({stream: true}));
});

gulp.task('server', function() {
	browserSync({
		server: {
		  baseDir: './public_html'
		}
	});
});

gulp.task('svg', function () {
	return gulp.src('src/svg/**/*.svg')
		.pipe(svgmin({
			plugins: [{
				removeDoctype: true
			}, {
				removeComments: true
			}, {
				cleanupNumericValues: {
					floatPrecision: 3
				}
			}, {
				convertColors: {
					names2hex: false,
					rgb2hex: false
				}
			}]
		}))
		.pipe(gulp.dest('./public_html'));
});

gulp.task('tinypng', function () {
    gulp.src('src/img/**/*.png')
        .pipe(tinypng('rznC3y4UtBfRBVDIEntK8WLAhBEAl6HX'))
        .pipe(gulp.dest('./public_html/'));
    gulp.src('src/img/**/*.jpg')
        .pipe(tinypng('rznC3y4UtBfRBVDIEntK8WLAhBEAl6HX'))
        .pipe(gulp.dest('./public_html/'));
});

gulp.task('watch', ['server'], function(){
	console.log(path);
	gulp.watch([path.html], ['ftp_upload']);
	gulp.watch([path.js], ['ftp_upload']);
	gulp.watch([path.sass], ['sass']);
});


gulp.task('default', ['watch']);
