const { src, dest } = require('gulp');
const imagemin = require('gulp-imagemin');
const rename = require("gulp-rename");
const webp = require('gulp-webp');

function imageMinTask() {
  return src('src/grondoycom/*')
  .pipe(imagemin())
  .pipe(webp())
  .pipe(dest('src/optimized'))
}


exports.default = imageMinTask;
