import path from 'path' //ayuda a las rutas entre sistemas operativos diferentes
import fs from 'fs'
import { glob } from 'glob'
import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import terser from 'gulp-terser'
import sharp from 'sharp'

const sass = gulpSass(dartSass)

const paths = {
    scss: 'src/scss/**/*.scss', // **/* esto es un patron de busqueda(glob pattern)le dice que archivos debe buscar
    js: 'src/js/**/*.js'
}

export function css( done ) {
    src(paths.scss, {sourcemaps: true})
        .pipe( sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError) )
        .pipe( dest('./public_html/build/css', {sourcemaps: '.'}) );
    done()
}

// export function js( done ) {
//     src(paths.js)
//       .pipe(terser())
//       .pipe(dest('./public/build/js'))
//     done()
// }

export function js() {//funcion para detectar si hay un error
    return src(paths.js)
        .pipe(terser())
        .on('error', function(error) {
            console.error('ERROR EN JAVASCRIPT:');
            console.error(error.message);
            console.error(error.filename);
            console.error('Línea:', error.line);
            console.error('Columna:', error.col);
            this.emit('end');
        })
        .pipe(dest('./public_html/build/js'));
}

export async function imagenes(done) {
    const srcDir = './src/img';
    const buildDir = './public_html/build/img';
    const images =  await glob('./src/img/**/*')

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file));
        const outputSubDir = path.join(buildDir, relativePath);
        procesarImagenes(file, outputSubDir);
    });
    done();
}

function procesarImagenes(file, outputSubDir) {
    if (!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }
    const baseName = path.basename(file, path.extname(file))
    const extName = path.extname(file)

    if (extName.toLowerCase() === '.svg') {
        // If it's an SVG file, move it to the output directory
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`);
    fs.copyFileSync(file, outputFile);
    } else {
        // For other image formats, process them with sharp
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`);
        const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`);
        const outputFileAvif = path.join(outputSubDir, `${baseName}.avif`);
        const options = { quality: 80 };

        sharp(file).jpeg(options).toFile(outputFile);
        sharp(file).webp(options).toFile(outputFileWebp);
        sharp(file).avif().toFile(outputFileAvif);
    }
}

export function dev() {
    watch( paths.scss, css );
    watch( paths.js, js );
    watch('src/img/**/*.{png,jpg,jpeg,svg}', imagenes)
}

export default series( js, css, imagenes, dev )
export const build = series( js, css, imagenes)