<?php
require_once __DIR__ . '/includes/functions.php';

$slug  = trim($_GET['slug']    ?? '');
$chNum = (int)($_GET['chapter'] ?? 0);

$comic = $slug ? getComic($slug) : null;
if (!$comic || !$chNum) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

// Find current, prev, next chapters
$chapters = $comic['chapters'] ?? [];
$chNums   = array_column($chapters, 'number');
$chIdx    = array_search($chNum, $chNums);

$prevCh   = null;
$nextCh   = null;
if ($chIdx !== false) {
    // chapters[0] = latest, so prev = higher index, next = lower index
    if ($chIdx + 1 < count($chapters)) $prevCh = $chapters[$chIdx + 1]['number'];
    if ($chIdx > 0)                     $nextCh = $chapters[$chIdx - 1]['number'];
}

// Local images
$imgDir   = __DIR__ . '/uploads/' . $slug . '/chapter-' . $chNum;
$imgWeb   = SITE_URL . '/uploads/' . rawurlencode($slug) . '/chapter-' . $chNum;
$images   = [];
if (is_dir($imgDir)) {
    $files = scandir($imgDir);
    foreach ($files as $f) {
        if (preg_match('/\.(jpg|jpeg|png|webp|gif|avif)$/i', $f)) {
            $images[] = $imgWeb . '/' . rawurlencode($f);
        }
    }
    natsort($images);
    $images = array_values($images);
}

$pageTitle = $comic['title'] . ' — Chapter ' . $chNum;

// Build chapter select options
$chOptions = '';
foreach ($chapters as $ch) {
    $sel = $ch['number'] === $chNum ? ' selected' : '';
    $chOptions .= '<option value="' . chapterUrl($comic, $ch['number']) . '"' . $sel . '>Chapter ' . $ch['number'] . '</option>';
}

ob_start();
?>

<!-- ── STICKY TOP NAV ── -->
<div class="sticky top-0 z-40" style="background:#1D1B22;border-bottom:1px solid rgba(255,255,255,.08)">
  <div class="mx-auto flex items-center justify-between gap-3 px-3 py-2.5 max-w-[1285px]">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm min-w-0 overflow-hidden">
      <a href="<?= SITE_URL ?>/" class="text-white/50 hover:text-white transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </a>
      <span class="text-white/30">›</span>
      <a href="<?= comicUrl($comic) ?>" class="text-white/70 hover:text-white transition-colors truncate max-w-[120px] md:max-w-none"><?= h($comic['title']) ?></a>
      <span class="text-white/30 flex-shrink-0">›</span>
      <span class="text-white flex-shrink-0">Ch.<?= $chNum ?></span>
    </div>

    <!-- Chapter selector + nav -->
    <div class="flex items-center gap-2 flex-shrink-0">
      <?php if ($prevCh !== null): ?>
      <a href="<?= chapterUrl($comic, $prevCh) ?>" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors text-white/80 hover:text-white hover:bg-white/10" title="Previous Chapter">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <span class="hidden sm:inline">Prev</span>
      </a>
      <?php endif; ?>

      <select onchange="location.href=this.value"
              class="bg-[#13111A] border border-white/10 rounded-lg px-2 py-1.5 text-sm text-white outline-none cursor-pointer max-w-[160px]">
        <?= $chOptions ?>
      </select>

      <?php if ($nextCh !== null): ?>
      <a href="<?= chapterUrl($comic, $nextCh) ?>" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors text-white/80 hover:text-white hover:bg-white/10" title="Next Chapter">
        <span class="hidden sm:inline">Next</span>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── READER CONTENT ── -->
<div id="reader-pages" style="background:#0D0B0E;min-height:60vh">
  <?php if (empty($images)): ?>
  <!-- No images available -->
  <div class="flex flex-col items-center justify-center py-24 text-center px-4">
    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6" style="background:#1D1B22">
      <svg class="w-10 h-10 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    </div>
    <h2 class="text-xl font-semibold text-white mb-2">Images Not Available</h2>
    <p class="text-white/50 text-sm max-w-md mb-2">Chapter <?= $chNum ?> images are not available locally.</p>
    <p class="text-white/30 text-xs mb-8 font-mono" style="background:#1D1B22;padding:.5rem 1rem;border-radius:.5rem">
      uploads/<?= h($slug) ?>/chapter-<?= $chNum ?>/
    </p>
    <div class="flex gap-3 flex-wrap justify-center">
      <a href="<?= comicUrl($comic) ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white" style="background:#913FE2">← Back to Comic</a>
      <?php if ($nextCh !== null): ?>
      <a href="<?= chapterUrl($comic, $nextCh) ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white border border-white/20" style="background:#2b2931">Next Chapter →</a>
      <?php endif; ?>
    </div>
  </div>
  <?php else: ?>
  <!-- Reader images -->
  <div style="max-width:800px;margin:0 auto">
    <?php foreach ($images as $src): ?>
    <img src="<?= h($src) ?>" alt="<?= h($comic['title']) ?> Ch.<?= $chNum ?>"
         class="reader-img" loading="lazy"
         onerror="this.style.display='none'">
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ── BOTTOM NAV ── -->
<div style="background:#1D1B22;border-top:1px solid rgba(255,255,255,.08)">
  <div class="mx-auto flex items-center justify-between gap-3 px-3 py-4 max-w-[1285px]">
    <a href="<?= comicUrl($comic) ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition-colors hover:bg-white/10">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Comic Page
    </a>
    <div class="flex items-center gap-2">
      <?php if ($prevCh !== null): ?>
      <a href="<?= chapterUrl($comic, $prevCh) ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white border border-white/20 hover:bg-white/10 transition-colors" style="background:#2b2931">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Previous
      </a>
      <?php endif; ?>
      <?php if ($nextCh !== null): ?>
      <a href="<?= chapterUrl($comic, $nextCh) ?>" class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white transition-colors" style="background:#913FE2">
        Next
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Keyboard navigation
document.addEventListener('keydown', function(e) {
  <?php if ($prevCh !== null): ?>
  if (e.key === 'ArrowLeft')  location.href = '<?= chapterUrl($comic, $prevCh) ?>';
  <?php endif; ?>
  <?php if ($nextCh !== null): ?>
  if (e.key === 'ArrowRight') location.href = '<?= chapterUrl($comic, $nextCh) ?>';
  <?php endif; ?>
});
// Scroll to top on load
window.addEventListener('load', function() { window.scrollTo(0,0); });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
