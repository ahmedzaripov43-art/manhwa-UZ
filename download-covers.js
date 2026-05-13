const fs   = require('fs');
const path = require('path');
const https = require('https');
const http  = require('http');

const data    = JSON.parse(fs.readFileSync(path.join(__dirname, 'data/comics.json'), 'utf8'));
const coversDir = path.join(__dirname, 'public/images/covers');

if (!fs.existsSync(coversDir)) fs.mkdirSync(coversDir, { recursive: true });

function download(url, dest) {
  return new Promise((resolve, reject) => {
    if (fs.existsSync(dest)) { resolve('skip'); return; }
    const file = fs.createWriteStream(dest);
    const mod  = url.startsWith('https') ? https : http;
    const req  = mod.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, res => {
      if (res.statusCode === 301 || res.statusCode === 302) {
        file.close();
        fs.unlinkSync(dest);
        download(res.headers.location, dest).then(resolve).catch(reject);
        return;
      }
      if (res.statusCode !== 200) {
        file.close();
        fs.unlinkSync(dest);
        reject(new Error('HTTP ' + res.statusCode));
        return;
      }
      res.pipe(file);
      file.on('finish', () => { file.close(); resolve('ok'); });
    });
    req.on('error', err => { file.close(); try { fs.unlinkSync(dest); } catch {} reject(err); });
    req.setTimeout(15000, () => { req.destroy(); reject(new Error('timeout')); });
  });
}

function extFromUrl(url) {
  const m = url.match(/\.(webp|gif|jpg|jpeg|png)(\?.*)?$/i);
  return m ? '.' + m[1].toLowerCase() : '.webp';
}

async function run() {
  const updated = [];
  for (const comic of data) {
    const ext  = extFromUrl(comic.cover);
    const file = comic.slug + ext;
    const dest = path.join(coversDir, file);
    try {
      const r = await download(comic.cover, dest);
      comic.cover = '/public/images/covers/' + file;
      console.log(`${r === 'skip' ? '⏭' : '✓'} ${comic.title}`);
    } catch (e) {
      console.log(`✗ ${comic.title}: ${e.message}`);
      // keep original URL if download fails
    }
    updated.push(comic);
  }
  fs.writeFileSync(path.join(__dirname, 'data/comics.json'), JSON.stringify(updated, null, 2));
  console.log('\nDone! comics.json updated with local cover paths.');
}

run();
