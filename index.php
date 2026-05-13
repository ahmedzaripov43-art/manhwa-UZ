<?php
require_once __DIR__ . '/includes/functions.php';

$curPage = 'home';
$all     = loadComics();

// Period tabs data
$topDay   = getTopByPeriod('day');
$topWeek  = getTopByPeriod('week');
$topMonth = getTopByPeriod('month');

// Latest updates — per-comic, most recent update first
$latestComics = getLatestUpdates(20);

// Popular sidebar data
$popular = getPopular(10);

ob_start();
?>

<div class="mx-auto mt-3 mb-6 w-full md:w-[95%] max-w-[1285px] px-3 md:px-0">

  <!-- Two-column layout: Trending + Latest | Popular Sidebar -->
  <div class="flex flex-col lg:flex-row lg:items-stretch gap-3 md:gap-4">

    <!-- ═══ LEFT COLUMN: Trending + Latest Updates ═══ -->
    <div class="lg:w-2/3 flex flex-col gap-3 md:gap-4">

      <!-- ── TRENDING (Today / Week / Month tabs) ── -->
      <section class="bg-[#1D1B22] md:rounded-md py-3 md:py-6">
        <!-- Tab Header -->
        <div class="px-4 md:px-6 flex items-center justify-between mb-3 md:mb-4">
          <h2 class="text-lg md:text-xl font-bold text-white">Trending</h2>
          <div class="flex gap-1 bg-[#13111A] rounded-lg p-1">
            <button onclick="switchTrend(this,'trend-day')"   data-trend="day"   class="trend-tab px-2 py-1 text-xs rounded-lg font-medium transition-all cursor-pointer bg-[#913FE2] text-white">Today</button>
            <button onclick="switchTrend(this,'trend-week')"  data-trend="week"  class="trend-tab px-2 py-1 text-xs rounded-lg font-medium transition-all cursor-pointer text-white/60 hover:text-white">Weekly</button>
            <button onclick="switchTrend(this,'trend-month')" data-trend="month" class="trend-tab px-2 py-1 text-xs rounded-lg font-medium transition-all cursor-pointer text-white/60 hover:text-white">Monthly</button>
          </div>
        </div>

        <!-- Today Panel -->
        <div id="trend-day" class="trend-panel">
          <?php if (empty($topDay)): ?>
          <div class="px-6 py-8 text-center text-white/50">No updates today — check back later.</div>
          <?php else: ?>
          <!-- Mobile: 2-col grid -->
          <div class="flex flex-wrap px-4 md:hidden">
            <?php foreach ($topDay as $c): ?>
            <?php $ch = $c['chapters'][0] ?? null; ?>
            <div class="w-1/2 p-1.5">
              <a href="<?= comicUrl($c) ?>" class="block group">
                <div class="relative overflow-hidden rounded-md" style="aspect-ratio:3/4">
                  <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover object-top" loading="lazy">
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                  <div class="absolute bottom-1.5 left-1.5 right-1.5">
                    <span class="text-[11px] text-white/80 font-medium"><?php if($ch) echo 'Ch.'.$ch['number']; ?></span>
                  </div>
                </div>
                <div class="mt-2">
                  <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                  <div class="flex items-center gap-1 text-[12px] text-[#999] font-medium mt-0.5">
                    <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                    <?= h((string)$c['rating']) ?>
                  </div>
                </div>
              </a>
            </div>
            <?php endforeach; ?>
          </div>
          <!-- Desktop: horizontal scroll -->
          <div class="hidden md:block">
            <div class="embla-trending overflow-x-auto px-6 pb-2" style="scrollbar-width:thin;scrollbar-color:#312f40 transparent">
              <div class="flex gap-3" style="min-width:max-content">
                <?php foreach ($topDay as $c): ?>
                <?php $ch = $c['chapters'][0] ?? null; ?>
                <a href="<?= comicUrl($c) ?>" class="block cursor-pointer group flex-shrink-0" style="width:150px">
                  <div class="relative overflow-hidden rounded-md group-hover:opacity-80 transition-opacity" style="aspect-ratio:3/4">
                    <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute top-1.5 right-1.5">
                      <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md" style="background:rgba(0,0,0,.6)">
                        <?= h(strtoupper($c['status'])) ?>
                      </span>
                    </div>
                  </div>
                  <div class="mt-2">
                    <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                    <?php if ($ch): ?>
                    <span class="block text-[12px] text-[#999] font-medium">Chapter <?= $ch['number'] ?></span>
                    <?php endif; ?>
                    <div class="flex items-center gap-0.5 text-[12px] text-[#999] font-bold mt-0.5">
                      <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                      <?= h((string)$c['rating']) ?>
                    </div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Week Panel (hidden) -->
        <div id="trend-week" class="trend-panel" hidden>
          <?php if (empty($topWeek)): ?>
          <div class="px-6 py-8 text-center text-white/50">No updates this week.</div>
          <?php else: ?>
          <div class="flex flex-wrap px-4 md:hidden">
            <?php foreach ($topWeek as $c): ?>
            <?php $ch = $c['chapters'][0] ?? null; ?>
            <div class="w-1/2 p-1.5">
              <a href="<?= comicUrl($c) ?>" class="block group">
                <div class="relative overflow-hidden rounded-md" style="aspect-ratio:3/4">
                  <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover object-top" loading="lazy">
                </div>
                <div class="mt-2">
                  <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                  <?php if ($ch): ?><span class="block text-[12px] text-[#999]">Ch.<?= $ch['number'] ?></span><?php endif; ?>
                </div>
              </a>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="hidden md:block">
            <div class="overflow-x-auto px-6 pb-2" style="scrollbar-width:thin;scrollbar-color:#312f40 transparent">
              <div class="flex gap-3" style="min-width:max-content">
                <?php foreach ($topWeek as $c): ?>
                <?php $ch = $c['chapters'][0] ?? null; ?>
                <a href="<?= comicUrl($c) ?>" class="block group flex-shrink-0" style="width:150px">
                  <div class="relative overflow-hidden rounded-md group-hover:opacity-80 transition-opacity" style="aspect-ratio:3/4">
                    <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover" loading="lazy">
                  </div>
                  <div class="mt-2">
                    <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                    <?php if ($ch): ?><span class="block text-[12px] text-[#999]">Ch.<?= $ch['number'] ?></span><?php endif; ?>
                    <div class="flex items-center gap-0.5 text-[12px] text-[#999] mt-0.5">
                      <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                      <?= h((string)$c['rating']) ?>
                    </div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Month Panel (hidden) -->
        <div id="trend-month" class="trend-panel" hidden>
          <?php if (empty($topMonth)): ?>
          <div class="px-6 py-8 text-center text-white/50">No updates this month.</div>
          <?php else: ?>
          <div class="flex flex-wrap px-4 md:hidden">
            <?php foreach ($topMonth as $c): ?>
            <?php $ch = $c['chapters'][0] ?? null; ?>
            <div class="w-1/2 p-1.5">
              <a href="<?= comicUrl($c) ?>" class="block group">
                <div class="relative overflow-hidden rounded-md" style="aspect-ratio:3/4">
                  <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover object-top" loading="lazy">
                </div>
                <div class="mt-2">
                  <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                  <?php if ($ch): ?><span class="block text-[12px] text-[#999]">Ch.<?= $ch['number'] ?></span><?php endif; ?>
                </div>
              </a>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="hidden md:block">
            <div class="overflow-x-auto px-6 pb-2" style="scrollbar-width:thin;scrollbar-color:#312f40 transparent">
              <div class="flex gap-3" style="min-width:max-content">
                <?php foreach ($topMonth as $c): ?>
                <?php $ch = $c['chapters'][0] ?? null; ?>
                <a href="<?= comicUrl($c) ?>" class="block group flex-shrink-0" style="width:150px">
                  <div class="relative overflow-hidden rounded-md group-hover:opacity-80 transition-opacity" style="aspect-ratio:3/4">
                    <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover" loading="lazy">
                  </div>
                  <div class="mt-2">
                    <span class="block text-[13px] font-bold text-white truncate group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
                    <?php if ($ch): ?><span class="block text-[12px] text-[#999]">Ch.<?= $ch['number'] ?></span><?php endif; ?>
                    <div class="flex items-center gap-0.5 text-[12px] text-[#999] mt-0.5">
                      <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                      <?= h((string)$c['rating']) ?>
                    </div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

      </section>

      <!-- ── LATEST UPDATES ── -->
      <section class="bg-[#1D1B22] h-full py-3 md:py-6 md:rounded-md">
        <div class="px-4 md:px-8 mb-3 md:mb-4 flex items-center justify-between">
          <h2 class="text-lg md:text-xl font-bold text-white">Latest Updates</h2>
          <a href="<?= browseUrl(['sort'=>'updated','page'=>'']) ?>" class="text-xs text-[#913FE2] hover:underline">View all →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 content-start px-4 md:px-8">
          <?php foreach ($latestComics as $c): ?>
          <?php $chapters = array_slice($c['chapters'] ?? [], 0, 3); ?>
          <div class="grid grid-cols-12 gap-2 py-4 px-2 border-b border-[#312f40]">
            <a href="<?= comicUrl($c) ?>" class="col-span-4 sm:col-span-3 md:col-span-4 lg:col-span-3 overflow-hidden rounded-md group">
              <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>"
                   class="w-[90%] sm:w-full object-cover rounded-md group-hover:opacity-60 transition-opacity"
                   style="aspect-ratio:3/4" loading="lazy">
            </a>
            <div class="col-span-8 sm:col-span-9 md:col-span-8 lg:col-span-9 flex flex-col min-w-0">
              <a href="<?= comicUrl($c) ?>" class="font-bold text-base line-clamp-1 hover:text-[#913FE2] transition-colors mb-2"><?= h($c['title']) ?></a>
              <div class="flex flex-col gap-1.5 sm:pl-2">
                <?php foreach ($chapters as $ch): ?>
                <a href="<?= chapterUrl($c, $ch['number']) ?>" class="flex items-center justify-between hover:text-[#913FE2] transition-colors group">
                  <div class="flex items-center min-w-0">
                    <span class="flex-1 min-w-0 truncate text-[14px] text-white/50">
                      <span class="group-hover:text-[#913FE2] font-medium text-white/90">Chapter <?= $ch['number'] ?></span>
                    </span>
                  </div>
                  <time class="text-white/60 text-[11px] whitespace-nowrap flex-shrink-0 ml-2"><?= h($ch['date']) ?></time>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

    </div><!-- /left column -->

    <!-- ═══ RIGHT COLUMN: Popular Sidebar ═══ -->
    <div class="lg:w-1/3 flex flex-col gap-3 md:gap-5">
      <section class="bg-[#1D1B22] px-4 md:px-5 pt-3 md:pt-5 pb-2 md:pb-3 md:rounded-md relative overflow-hidden">
        <!-- Header -->
        <div class="mb-3 md:mb-5">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Popular</h2>
            <div class="flex gap-1 bg-[#13111A] rounded-lg p-1">
              <button onclick="switchPopular(this,'pop-week')"  class="pop-tab px-2 py-1 text-xs rounded-lg transition-all font-medium cursor-pointer bg-[#913FE2] text-white">Weekly</button>
              <button onclick="switchPopular(this,'pop-month')" class="pop-tab px-2 py-1 text-xs rounded-lg transition-all font-medium cursor-pointer text-white/60 hover:text-white">Monthly</button>
              <button onclick="switchPopular(this,'pop-all')"   class="pop-tab px-2 py-1 text-xs rounded-lg transition-all font-medium cursor-pointer text-white/60 hover:text-white">All Time</button>
            </div>
          </div>
        </div>

        <!-- Weekly Panel -->
        <div id="pop-week" class="pop-panel flex flex-col gap-3">
          <?php foreach (getPopular(10,'week') as $i => $c): ?>
          <?php $ch = $c['chapters'][0] ?? null; ?>
          <a href="<?= comicUrl($c) ?>" class="flex gap-3 px-2 py-2.5 relative rounded-lg hover:bg-white/[0.03] transition-colors group">
            <div class="relative flex-shrink-0">
              <div class="overflow-hidden rounded-lg">
                <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>"
                     class="w-12 md:w-14 h-16 md:h-[72px] object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
              </div>
              <div class="absolute -top-1 -left-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#913FE2"><?= $i+1 ?></div>
            </div>
            <div class="flex-1 min-w-0 overflow-hidden">
              <span class="block text-[14px] font-medium text-white leading-tight line-clamp-2 group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
              <p class="text-[12px] text-[#888] mt-1">
                <?php if (!empty($c['genres'])): ?>
                <span class="text-white/80"><?= h($c['genres'][0]) ?><?= isset($c['genres'][1]) ? ', '.h($c['genres'][1]) : '' ?></span>
                <?php endif; ?>
              </p>
              <div class="flex items-center gap-3 mt-1.5 text-[11px] text-[#888]">
                <?php if ($ch): ?>
                <span>Ch.<?= $ch['number'] ?></span>
                <?php endif; ?>
                <div class="flex items-center gap-0.5">
                  <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                  <?= h((string)$c['rating']) ?>
                </div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- Monthly Panel (hidden) -->
        <div id="pop-month" class="pop-panel flex flex-col gap-3" hidden>
          <?php foreach (getPopular(10,'month') as $i => $c): ?>
          <?php $ch = $c['chapters'][0] ?? null; ?>
          <a href="<?= comicUrl($c) ?>" class="flex gap-3 px-2 py-2.5 rounded-lg hover:bg-white/[0.03] transition-colors group">
            <div class="relative flex-shrink-0">
              <div class="overflow-hidden rounded-lg">
                <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>"
                     class="w-12 md:w-14 h-16 md:h-[72px] object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
              </div>
              <div class="absolute -top-1 -left-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#913FE2"><?= $i+1 ?></div>
            </div>
            <div class="flex-1 min-w-0">
              <span class="block text-[14px] font-medium text-white line-clamp-2 group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
              <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#888]">
                <?php if ($ch): ?><span>Ch.<?= $ch['number'] ?></span><?php endif; ?>
                <div class="flex items-center gap-0.5"><svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg><?= h((string)$c['rating']) ?></div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- All Time Panel (hidden) -->
        <div id="pop-all" class="pop-panel flex flex-col gap-3" hidden>
          <?php foreach (getPopular(10,'all') as $i => $c): ?>
          <?php $ch = $c['chapters'][0] ?? null; ?>
          <a href="<?= comicUrl($c) ?>" class="flex gap-3 px-2 py-2.5 rounded-lg hover:bg-white/[0.03] transition-colors group">
            <div class="relative flex-shrink-0">
              <div class="overflow-hidden rounded-lg">
                <img src="<?= h(coverUrl($c)) ?>" alt="<?= h($c['title']) ?>"
                     class="w-12 md:w-14 h-16 md:h-[72px] object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
              </div>
              <div class="absolute -top-1 -left-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#913FE2"><?= $i+1 ?></div>
            </div>
            <div class="flex-1 min-w-0">
              <span class="block text-[14px] font-medium text-white line-clamp-2 group-hover:text-[#913FE2] transition-colors"><?= h($c['title']) ?></span>
              <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#888]">
                <?php if ($ch): ?><span>Ch.<?= $ch['number'] ?></span><?php endif; ?>
                <div class="flex items-center gap-0.5"><svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg><?= h((string)$c['rating']) ?></div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <div class="text-center pt-3 pb-1">
          <a href="<?= browseUrl(['sort'=>'rating']) ?>" class="text-xs text-[#913FE2] hover:underline">View all popular →</a>
        </div>
      </section>
    </div><!-- /right column -->

  </div><!-- /two-column -->
</div><!-- /container -->

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
