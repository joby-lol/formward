var sass = require('node-sass');
var fs = require('fs');

/* Build Sass files */

var sassFiles = {
  './src/scss/core.scss': [
    './build/core.css',
    '../src/_resources/core.css'
  ]
};

for (var srcFile in sassFiles) {
  if (sassFiles.hasOwnProperty(srcFile)) {
    out = sass.renderSync({
      file: srcFile
    });
    for (var outFile in sassFiles[srcFile]) {
      if (sassFiles[srcFile].hasOwnProperty(outFile)) {
        fs.writeFileSync(sassFiles[srcFile][outFile],out.css.toString());
      }
    }
  }
}

/* Copy JS files */

function copyFile(src,dest) {
  fs.createReadStream(src).pipe(fs.createWriteStream(dest));
}

var path = 'src/js';
fs.readdir(
  './src/js',
  function (err, items) {
    for (var i = 0; i < items.length; i++) {
      console.log(items[i]);
      copyFile(
        './src/js/'+items[i],
        './build/'+items[i]
      );
      copyFile(
        './src/js/'+items[i],
        '../src/_resources/'+items[i]
      );
    }
  }
);
