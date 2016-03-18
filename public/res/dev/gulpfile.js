'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
//var watch = require('gulp-watch');


/*
* SASS
*/
gulp.task('sass', function(){
    gulp.src('scss/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest('../css'));
});

// Watch sass
gulp.task('watch', function(){
    gulp.watch('scss/**/*.scss', ['sass']);
    gulp.watch('js/*.js', ['uglify']);
});


/*
* UGLIFY
*/
gulp.task('uglify', function(){
    return gulp.src([
            'js/jquery-1.11.3.min.js',
            'js/jquery.bxslider.js',
            'js/jquery.validate.js',
            'js/jquery.mixitup.js',
            'js/functions.js'])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('../js'));
});


// Default task
gulp.task('default', ['watch', 'uglify']);