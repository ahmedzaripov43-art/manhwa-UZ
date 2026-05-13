<?php
require_once __DIR__ . '/includes/functions.php';

$curPage = 'browse';

$q      = trim($_GET['q']      ?? '');
$genre  = trim($_GET['genre']  ?? '');
$status = trim($_GET['status'] ?? '');
$sort   = trim($_GET['sort']   ?? 'updated');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 24;

if ($q !== '') {
    $comics = searchComics($q);
} else {
    $comics = filterComics($genre, $status, $sort);
}

$paged    = paginate($comics, $perPage, $page);
$items    = $paged['items'];
$total    = $paged['total'];
$pages    = $paged['pages'];
$page     = $paged['page'];

$genres   = allGenres();
$urlFn = fn(int $p) => browseUrl(['page' => $p]);

$pageTitle = $q !== '' ? 'Search: ' . $q : 'Browse';
ob_start();
?>

<div class="min-h-screen">
  <div class="mx-auto w-full md:w-[95%] max-w-[1285px] px-3 md:px-0 py-6">

    <!-- ── Filters ── -->
    <div class="bg-[#1D1B22] rounded-xl p-4 md:p-6 mb-6">
      <form method="get" action="<?= SITE_URL ?>/browse" id="browse-form">

        <!-- Search input -->
        <div class="relative mb-4">
          <input type="search" name="q" value="<?= h($q) ?>" placeholder="Search manga, manhwa…"
                 class="w-full bg-[#13111A] border border-white/10 rounded-lg px-4 py-3 pl-10 text-sm text-white placeholder:text-white/40 outline-none focus:border-[#913FE2] transition-colors">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <?php if ($q !== ''): ?>
          <a href="<?= browseUrl(['q'=>'','page'=>'']) ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white text-lg leading-none">×</a>
          <?php endif; ?>
        </div>

        <!-- Dropdowns -->
        <div class="flex flex-wrap gap-3">
          <div class="flex-1 min-w-[140px]">
            <label class="block text-xs text-white/50 mb-1.5">Genre</label>
            <select name="genre" onchange="document.getElementById('browse-form').submit()"
                    class="w-full bg-[#13111A] border border-white/10 rounded-lg px-3 py-2 text-sm text-white outline-none cursor-pointer">
              <option value="" <?= $genre==='' ? 'selected' : '' ?>>All Genres</option>
              <?php foreach ($genres as $g): ?>
              <option value="<?= h($g) ?>" <?= $genre===$g ? 'selected' : '' ?>><?= h($g) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="flex-1 min-w-[130px]">
            <label class="block text-xs text-white/50 mb-1.5">Status</label>
            <select name="status" onchange="document.getElementById('browse-form').submit()"
                    class="w-full bg-[#13111A] border border-white/10 rounded-lg px-3 py-2 text-sm text-white outline-none cursor-pointer">
              <option value="" <?= $status==='' ? 'selected' : '' ?>>All Status</option>
              <option value="Ongoing"   <?= $status==='Ongoing'   ? 'selected' : '' ?>>Ongoing</option>
              <option value="Completed" <?= $status==='Completed' ? 'selected' : '' ?>>Completed</option>
              <option value="Hiatus"    <?= $status==='Hiatus'    ? 'selected' : '' ?>>Hiatus</option>
              <option value="Dropped"   <?= $status==='Dropped'   ? 'selected' : '' ?>>Dropped</option>
            </select>
          </div>
          <div class="flex-1 min-w-[130px]">
            <label class="block text-xs text-white/50 mb-1.5">Sort By</label>
            <select name="sort" onchange="document.getElementById('browse-form').submit()"
                    class="w-full bg-[#13111A] border border-white/10 rounded-lg px-3 py-2 text-sm text-white outline-none cursor-pointer">
              <option value="updated"  <?= $sort==='updated'  ? 'selected' : '' ?>>Latest Updated</option>
              <option value="rating"   <?= $sort==='rating'   ? 'selected' : '' ?>>Highest Rated</option>
              <option value="chapters" <?= $sort==='chapters' ? 'selected' : '' ?>>Most Chapters</option>
              <option value="title"    <?= $sort==='title'    ? 'selected' : '' ?>>Title (A–Z)</option>
            </select>
          </div>
          <input type="hidden" name="page" value="1">
        </div>

      </form>
    </div>

    <!-- ── Genre pills ── -->
    <div class="flex flex-wrap gap-2 mb-5">
      <a href="<?= SITE_URL ?>/browse" class="inline-flex text-xs font-medium px-3 py-1.5 rounded-lg border transition-all <?= $genre==='' ? 'border-[#913FE2] text-[#913FE2] bg-[#913FE2]/10' : 'border-white/10 bg-white/5 text-white/70 hover:border-[#913FE2] hover:text-[#913FE2]' ?>">All</a>
      <?php foreach (array_slice($genres, 0, 20) as $g): ?>
      <a href="<?= browseUrl(['genre'=>$g,'page'=>'']) ?>" class="inline-flex text-xs font-medium px-3 py-1.5 rounded-lg border transition-all <?= $genre===$g ? 'border-[#913FE2] text-[#913FE2] bg-[#913FE2]/10' : 'border-white/10 bg-white/5 text-white/70 hover:border-[#913FE2] hover:text-[#913FE2]' ?>"><?= h($g) ?></a>
      <?php endforeach; ?>
    </div>

    <!-- ── Results count ── -->
    <div class="flex items-center justify-between mb-4">
      <p class="text-sm text-white/60">
        <?php if ($q !== ''): ?>
        <span class="text-white font-medium"><?= $total ?></span> results for "<span class="text-[#913FE2]"><?= h($q) ?></span>"
        <?php else: ?>
        <span class="text-white font-medium"><?= $total ?></span> series<?php if ($genre !== ''): ?> in <span class="text-[#913FE2]"><?= h($genre) ?></span><?php endif; ?>
        <?php endif; ?>
      </p>
      <?php if ($q !== '' || $genre !== '' || $status !== ''): ?>
      <a href="<?= SITE_URL ?>/browse" class="text-xs text-white/50 hover:text-white">Clear filters</a>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
    <div class="text-center py-20">
      <div class="text-5xl mb-4">📭</div>
      <h3 class="text-xl font-semibold text-white mb-2">No results found</h3>
      <p class="text-white/50 mb-6">Try different keywords or remove some filters.</p>
      <a href="<?= SITE_URL ?>/browse" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium text-white" style="background:#913FE2">Browse All</a>
    </div>
    <?php else: ?>

    <!-- ── Comic Grid ── -->
    <div class="browse-grid">
      <?php foreach ($items as $c): ?>
      <?php $ch = $c['chapters'][0] ?? null; ?>
      <a href="<?= comicUrl($c) ?>" class="group block">
        <div class="relative overflow-hidden rounded-lg" style="aspect-ratio:2/3">
          <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>"
               class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
          <!-- Status top-left -->
          <div class="absolute top-1.5 left-1.5">
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md text-white" style="background:rgba(0,0,0,.7)"><?= h(ucfirst($c['status'] ?? 'Ongoing')) ?></span>
          </div>
          <!-- Rating top-right -->
          <div class="absolute top-1.5 right-1.5 flex items-center gap-0.5 px-1.5 py-0.5 rounded-md text-[11px] font-bold text-yellow-400" style="background:rgba(0,0,0,.7)">
            <svg class="w-2.5 h-2.5 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
            <?= h((string)$c['rating']) ?>
          </div>
          <!-- Chapter at bottom -->
          <?php if ($ch): ?>
          <div class="absolute bottom-0 left-0 right-0 px-2 pb-2 bg-gradient-to-t from-black/90 to-transparent pt-6">
            <span class="text-[11px] text-white/90 font-medium">Ch.<?= $ch['number'] ?></span>
          </div>
          <?php endif; ?>
        </div>
        <div class="mt-2 px-0.5">
          <h3 class="text-[13px] font-semibold text-white line-clamp-2 group-hover:text-[#913FE2] transition-colors leading-tight"><?= h($c['title']) ?></h3>
          <?php if (!empty($c['genres'][0])): ?>
          <p class="text-[11px] text-white/40 mt-1 truncate"><?= h($c['genres'][0]) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- ── Pagination ── -->
    <?= renderPagination($page, $pages, $urlFn) ?>

    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
