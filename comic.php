<?php
require_once __DIR__ . '/includes/functions.php';

$slug  = trim($_GET['slug'] ?? '');
$comic = $slug ? getComic($slug) : null;

if (!$comic) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$chapters = $comic['chapters'] ?? [];
$firstCh  = !empty($chapters) ? end($chapters) : null;
$latestCh = !empty($chapters) ? $chapters[0]   : null;

// Chapter pagination
$chPage  = max(1, (int)($_GET['chpage'] ?? 1));
$chPer   = 50;
$chTotal = count($chapters);
$chPages = max(1, (int)ceil($chTotal / $chPer));
$chPage  = min($chPage, $chPages);
$chSlice = array_slice($chapters, ($chPage - 1) * $chPer, $chPer);

$pageTitle = $comic['title'];
$pageDesc  = substr(strip_tags($comic['description'] ?? ''), 0, 160);
$ogImage   = coverUrl($comic);

ob_start();
?>

<div class="relative">
  <!-- Desktop: background banner blur -->
  <div class="hidden lg:block absolute top-0 left-0 w-full overflow-hidden" style="height:450px;margin-top:-1px;z-index:-1">
    <img src="<?= h(coverUrl($comic)) ?>" alt="" aria-hidden="true"
         class="w-full h-full object-cover pointer-events-none select-none"
         style="filter:blur(6px);transform:scale(1.1)">
    <div class="absolute inset-0" style="background:rgba(0,0,0,.5)"></div>
    <div class="absolute inset-0" style="background:linear-gradient(to bottom,transparent 40%,#0D0B0E 100%)"></div>
  </div>

  <!-- Mobile cover banner -->
  <div class="lg:hidden relative flex items-center justify-center pt-4" style="height:300px">
    <div class="absolute inset-x-0 top-0 overflow-hidden" style="height:500px;z-index:-1">
      <img src="<?= h(coverUrl($comic)) ?>" alt="" aria-hidden="true"
           class="w-full h-full object-cover pointer-events-none select-none"
           style="filter:blur(4px);transform:scale(1.05)">
      <div class="absolute inset-0" style="background:rgba(0,0,0,.5)"></div>
      <div class="absolute inset-0" style="background:linear-gradient(to bottom,transparent 40%,#0D0B0E 100%)"></div>
    </div>
    <div class="relative rounded-lg overflow-hidden shadow-2xl" style="width:180px;aspect-ratio:2/3">
      <img src="<?= h(coverUrl($comic)) ?>" alt="<?= h($comic['title']) ?>" class="w-full h-full object-cover">
    </div>
  </div>

  <!-- Content -->
  <div class="mx-auto w-full md:w-[95%] max-w-[1285px] px-3 lg:px-0 relative pt-0 lg:pt-8 mb-5 md:mb-7" style="z-index:10">
    <div class="lg:flex lg:gap-9">

      <!-- Left: Cover (Desktop) -->
      <div class="lg:max-w-[400px] w-full flex flex-col gap-3 relative">
        <div class="hidden lg:block relative overflow-hidden rounded-lg w-full" style="aspect-ratio:2/3">
          <img src="<?= h(coverUrl($comic)) ?>" alt="<?= h($comic['title']) ?>"
               class="w-full h-full object-cover">
        </div>

        <!-- Action Buttons (Mobile) -->
        <div class="grid grid-cols-2 gap-2 text-sm font-semibold lg:hidden">
          <?php if ($firstCh): ?>
          <a href="<?= chapterUrl($comic, $firstCh['number']) ?>"
             class="py-3 rounded-md flex items-center justify-center gap-2 transition-colors text-black"
             style="background:#E8E8E8">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            <span>First Chapter</span>
          </a>
          <?php endif; ?>
          <?php if ($latestCh): ?>
          <a href="<?= chapterUrl($comic, $latestCh['number']) ?>"
             class="py-3 rounded-md flex items-center justify-center gap-2 transition-colors text-white border border-white/20"
             style="background:#2b2931">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            <span>Latest Chapter</span>
          </a>
          <?php endif; ?>
        </div>

        <!-- Action Buttons (Desktop) -->
        <div class="hidden lg:grid gap-3 mt-4 font-semibold" style="grid-template-columns:1fr 1fr;height:3rem">
          <?php if ($firstCh): ?>
          <a href="<?= chapterUrl($comic, $firstCh['number']) ?>"
             class="h-full rounded-md flex items-center justify-center gap-2 transition-colors text-black"
             style="background:#E8E8E8">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            First Chapter
          </a>
          <?php endif; ?>
          <?php if ($latestCh): ?>
          <a href="<?= chapterUrl($comic, $latestCh['number']) ?>"
             class="h-full rounded-md flex items-center justify-center gap-2 transition-colors text-white border border-white/20"
             style="background:#2b2931">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            Latest Chapter
          </a>
          <?php endif; ?>
        </div>
      </div><!-- /left -->

      <!-- Right: Info -->
      <div class="w-full mt-3 lg:mt-0">
        <article class="bg-[#1C1924] rounded-lg px-3 py-4 lg:p-8">
          <h1 class="text-xl lg:text-[32px] font-semibold leading-tight"><?= h($comic['title']) ?></h1>

          <!-- Alternate titles -->
          <?php if (!empty($comic['alternateNames'])): ?>
          <div class="flex items-start gap-3 mt-3 text-xs">
            <p class="text-white/50 leading-relaxed line-clamp-1"><?= h($comic['alternateNames']) ?></p>
          </div>
          <?php endif; ?>

          <!-- Rating + views row -->
          <div class="flex items-center gap-4 mt-3 text-sm">
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
              <span class="font-bold text-white"><?= h((string)$comic['rating']) ?></span>
              <span class="text-white/40">(<?= number_format($comic['ratingCount'] ?? 0) ?> ratings)</span>
            </div>
            <div class="flex items-center gap-1.5 text-white/50">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
              <?= number_format($comic['episodeCount'] ?? 0) ?> chapters
            </div>
          </div>

          <!-- Description -->
          <?php if (!empty($comic['description'])): ?>
          <div class="mt-4 relative">
            <div id="desc-text" class="text-sm lg:text-base font-light text-white/80 leading-relaxed line-clamp-3">
              <?= nl2br(h(strip_tags($comic['description']))) ?>
            </div>
            <div class="flex justify-end mt-2">
              <button id="desc-toggle" onclick="toggleDesc()" class="flex items-center gap-1 text-xs font-medium px-2 py-1 rounded transition-colors hover:bg-white/5" style="color:#913FE2">
                <span id="desc-label">Show more</span>
                <svg id="desc-icon" class="w-3 h-3 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
          <?php endif; ?>

          <!-- Status + Type -->
          <div class="flex gap-3 pt-4 border-t border-white/10 mt-4">
            <div class="flex-1 bg-[#1C1924] rounded px-4 py-3" style="border:1px solid rgba(255,255,255,.07)">
              <div class="text-xs text-white/50 mb-1">Status</div>
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" style="background:#A78BFA"></span>
                <span class="text-base font-bold capitalize" style="color:#A78BFA"><?= h(strtolower($comic['status'] ?? 'ongoing')) ?></span>
              </div>
            </div>
            <div class="flex-1 bg-[#1C1924] rounded px-4 py-3" style="border:1px solid rgba(255,255,255,.07)">
              <div class="text-xs text-white/50 mb-1">Chapters</div>
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" style="background:#913FE2"></span>
                <span class="text-base font-bold" style="color:#913FE2"><?= $chTotal ?></span>
              </div>
            </div>
          </div>

          <!-- Creators -->
          <div class="flex flex-col gap-2 pt-4 border-t border-white/10 mt-4">
            <?php if (!empty($comic['author'])): ?>
            <div class="flex items-center justify-between bg-[#1C1924] rounded px-4 py-2.5" style="border:1px solid rgba(255,255,255,.07)">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span class="text-xs text-white/50">Author</span>
              </div>
              <span class="text-sm font-medium"><?= h($comic['author']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($comic['illustrator'])): ?>
            <div class="flex items-center justify-between bg-[#1C1924] rounded px-4 py-2.5" style="border:1px solid rgba(255,255,255,.07)">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-xs text-white/50">Artist</span>
              </div>
              <span class="text-sm font-medium"><?= h($comic['illustrator']) ?></span>
            </div>
            <?php endif; ?>
          </div>

          <!-- Genres -->
          <?php if (!empty($comic['genres'])): ?>
          <div class="pt-4 border-t border-white/10 mt-4">
            <div class="text-xs text-white/50 mb-2">Genres</div>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($comic['genres'] as $g): ?>
              <a href="<?= browseUrl(['genre'=>$g,'page'=>'']) ?>"
                 class="inline-flex text-xs font-medium px-3 py-1.5 rounded-lg border border-white/10 bg-white/5 hover:border-[#913FE2] hover:text-[#913FE2] transition-all">
                <?= h($g) ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

        </article>

        <!-- ── Chapter List ── -->
        <div class="mt-4">
          <div class="bg-[#1C1924] rounded-lg overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-white/10 flex items-center justify-between">
              <h2 class="text-base font-semibold">Chapters <span class="text-white/40 text-sm font-normal">(<?= $chTotal ?>)</span></h2>
              <?php if ($chPages > 1): ?>
              <div class="flex items-center gap-1 text-xs text-white/50">
                Page <?= $chPage ?> / <?= $chPages ?>
              </div>
              <?php endif; ?>
            </div>

            <?php if (empty($chapters)): ?>
            <div class="px-6 py-10 text-center text-white/50">No chapters available yet.</div>
            <?php else: ?>
            <div class="divide-y divide-white/5">
              <?php foreach ($chSlice as $ch): ?>
              <a href="<?= chapterUrl($comic, $ch['number']) ?>"
                 class="flex items-center justify-between px-4 md:px-6 py-3 hover:bg-white/[0.03] transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                  <svg class="w-4 h-4 text-white/30 flex-shrink-0 group-hover:text-[#913FE2] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  <span class="text-sm font-medium group-hover:text-[#913FE2] transition-colors">Chapter <?= $ch['number'] ?></span>
                </div>
                <time class="text-xs text-white/40 flex-shrink-0 ml-4"><?= h($ch['date']) ?></time>
              </a>
              <?php endforeach; ?>
            </div>

            <!-- Chapter pagination -->
            <?php if ($chPages > 1): ?>
            <div class="px-4 py-4 flex justify-center gap-1 border-t border-white/10 flex-wrap">
              <?php if ($chPage > 1): ?>
              <a href="?<?= http_build_query(['slug'=>$slug,'chpage'=>$chPage-1]) ?>" class="page-btn">‹</a>
              <?php endif; ?>
              <?php
              $chRange = range(max(1,$chPage-2), min($chPages,$chPage+2));
              if (!in_array(1,$chRange)) {
                  echo '<a href="?'.http_build_query(['slug'=>$slug,'chpage'=>1]).'" class="page-btn">1</a>';
                  if ($chRange[0]>2) echo '<span class="page-dots">…</span>';
              }
              foreach ($chRange as $p):
                  $cls = $p===$chPage ? ' active' : '';
                  echo '<a href="?'.http_build_query(['slug'=>$slug,'chpage'=>$p]).'" class="page-btn'.$cls.'">'.$p.'</a>';
              endforeach;
              if (!in_array($chPages,$chRange)) {
                  if ($chRange[count($chRange)-1]<$chPages-1) echo '<span class="page-dots">…</span>';
                  echo '<a href="?'.http_build_query(['slug'=>$slug,'chpage'=>$chPages]).'" class="page-btn">'.$chPages.'</a>';
              }
              ?>
              <?php if ($chPage < $chPages): ?>
              <a href="?<?= http_build_query(['slug'=>$slug,'chpage'=>$chPage+1]) ?>" class="page-btn">›</a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>

          </div>
        </div><!-- /chapters -->
      </div><!-- /right -->
    </div>
  </div>
</div>

<script>
function toggleDesc() {
  var d = document.getElementById('desc-text');
  var l = document.getElementById('desc-label');
  var i = document.getElementById('desc-icon');
  var expanded = d.style.webkitLineClamp === 'unset';
  if (expanded) {
    d.style.webkitLineClamp = '3';
    d.style.display = '-webkit-box';
    l.textContent = 'Show more';
    i.style.transform = '';
  } else {
    d.style.webkitLineClamp = 'unset';
    d.style.display = 'block';
    l.textContent = 'Show less';
    i.style.transform = 'rotate(180deg)';
  }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
