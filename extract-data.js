const fs   = require('fs');
const path = require('path');

const comicsDir  = path.join(__dirname, '../asurascans.com/comics');
const outputFile = path.join(__dirname, 'data', 'comics.json');

/* ── JSON-LD extraction ── */
function extractJsonLd(html) {
  const results = [];
  const re = /<script type="application\/ld\+json">([\s\S]*?)<\/script>/g;
  let m;
  while ((m = re.exec(html)) !== null) {
    try { results.push(JSON.parse(m[1])); } catch {}
  }
  return results;
}

/* ── Relative date → Unix timestamp ──
   Base time = the ISO lastUpdated date of each comic (scrape time)
*/
function relToTs(relDate, baseIso) {
  const base = baseIso ? Math.floor(new Date(baseIso).getTime() / 1000) : Math.floor(Date.now() / 1000);
  const s    = (relDate || '').toLowerCase().trim();

  if (!s || s === 'just now' || s === 'now') return base;

  let m;
  if ((m = s.match(/(\d+)\s*hour/)))  return base - m[1] * 3600;
  if ((m = s.match(/(\d+)\s*day/)))   return base - m[1] * 86400;
  if (s === 'last week')               return base - 7  * 86400;
  if ((m = s.match(/(\d+)\s*week/)))  return base - m[1] * 7 * 86400;
  if (s === 'last month')              return base - 30 * 86400;
  if ((m = s.match(/(\d+)\s*month/))) return base - m[1] * 30 * 86400;
  if (s === 'last year')               return base - 365 * 86400;
  if ((m = s.match(/(\d+)\s*year/)))  return base - m[1] * 365 * 86400;

  return base;
}

/* ── Chapter list extraction ── */
function extractChapters(html, baseIso) {
  const chapters = [];
  const re = /href="https:\/\/asurascans\.com\/comics\/([^"]+)\/chapter\/(\d+)"[^>]*>[\s\S]*?Chapter <!-- -->(\d+)[\s\S]*?<span[^>]*text-white\/40[^>]*>([^<]+)<\/span>/g;
  const seen = new Set();
  let m;
  while ((m = re.exec(html)) !== null) {
    const num = parseInt(m[2]);
    if (seen.has(num)) continue;
    seen.add(num);
    const relDate = m[4].trim();
    chapters.push({
      number:    num,
      date:      relDate,
      timestamp: relToTs(relDate, baseIso),   // ← absolute Unix timestamp
    });
  }
  return chapters.sort((a, b) => b.number - a.number);
}

/* ── Status ── */
function extractStatus(html) {
  const VALID = ['Ongoing', 'Completed', 'Hiatus', 'Dropped'];
  const m = html.match(/Status<\/span>[\s\S]*?<span[^>]*>([^<]+)<\/span>/);
  if (m) {
    const val = m[1].trim();
    if (VALID.includes(val)) return val;
  }
  if (html.includes('>Completed<')) return 'Completed';
  if (html.includes('>Hiatus<'))    return 'Hiatus';
  return 'Ongoing'; // safe default
}

/* ── Cover: prefer local path ── */
function localCoverPath(slug) {
  const exts  = ['webp', 'gif', 'png', 'jpg'];
  for (const ext of exts) {
    const fp = path.join(__dirname, 'public/images/covers', `${slug}.${ext}`);
    if (fs.existsSync(fp)) return `/public/images/covers/${slug}.${ext}`;
  }
  return null;
}

function extractSlug(filename) {
  return filename.replace(/-[a-f0-9]{8}\.html$/, '');
}

/* ════ MAIN ════ */
const files  = fs.readdirSync(comicsDir).filter(f => f.endsWith('.html'));
const comics = [];

for (const file of files) {
  const html    = fs.readFileSync(path.join(comicsDir, file), 'utf8');
  const jsonLds = extractJsonLd(html);
  const series  = jsonLds.find(j => j['@type'] === 'ComicSeries');
  const article = jsonLds.find(j => j['@type'] === 'Article');
  if (!series) continue;

  const slug        = extractSlug(file);
  const lastUpdated = article?.dateModified || '';
  const chapters    = extractChapters(html, lastUpdated);

  // Latest chapter timestamp (for period filtering)
  const latestTs = chapters.length ? chapters[0].timestamp : 0;

  // Local cover preferred, CDN fallback
  const localCover = localCoverPath(slug);
  const cover      = localCover || series.image || article?.image?.url || '';

  comics.push({
    slug,
    title:          series.name || article?.headline || '',
    alternateNames: series.alternateName || '',
    description:    series.description || article?.description || '',
    cover,
    genres:         Array.isArray(series.genre) ? series.genre : [],
    author:         series.author?.name || '',
    illustrator:    series.illustrator?.name || '',
    episodeCount:   series.numberOfEpisodes || 0,
    rating:         series.aggregateRating?.ratingValue || '0',
    ratingCount:    series.aggregateRating?.ratingCount || 0,
    status:         extractStatus(html),
    lastUpdated,
    latestTs,       // ← Unix timestamp of most recent chapter
    chapters,
  });

  console.log(`✓ ${series.name} — ${chapters.length} ch, latestTs=${new Date(latestTs*1000).toDateString()}`);
}

comics.sort((a, b) => a.title.localeCompare(b.title));

fs.mkdirSync(path.join(__dirname, 'data'), { recursive: true });
fs.writeFileSync(outputFile, JSON.stringify(comics, null, 2));
console.log(`\nDone! ${comics.length} comics → data/comics.json`);
