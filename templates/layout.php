<?php
$pageTitle = isset($pageTitle)
    ? h($pageTitle) . ' | ' . SITE_NAME
    : SITE_NAME . ' — Read Manga &amp; Manhwa Free';
$pageDesc  = $pageDesc ?? 'Read manga, manhwa and manhua online for free. Latest chapters updated daily.';
$curPage   = $curPage  ?? '';
?><!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?= $pageTitle ?></title>
  <meta name="description" content="<?= h($pageDesc) ?>">
  <meta name="theme-color" content="#913FE2">
  <!-- OG -->
  <meta property="og:site_name" content="<?= SITE_NAME ?>">
  <meta property="og:type"      content="website">
  <meta property="og:title"     content="<?= $pageTitle ?>">
  <meta property="og:description" content="<?= h($pageDesc) ?>">
  <?php if (!empty($ogImage)): ?>
  <meta property="og:image" content="<?= h($ogImage) ?>">
  <meta name="twitter:card"  content="summary_large_image">
  <meta name="twitter:image" content="<?= h($ogImage) ?>">
  <?php endif; ?>
  <!-- Styles: Tailwind (asura.css) first, custom overrides second -->
  <link rel="stylesheet" href="/public/css/asura.css">
  <link rel="stylesheet" href="/public/css/style.css">
  <?php if (!empty($headExtra)) echo $headExtra; ?>
</head>
<body class="min-h-screen bg-[#13111A] text-white flex flex-col" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',sans-serif;-webkit-font-smoothing:antialiased">

<!-- ═══ MOBILE NAV OVERLAY ═══ -->
<div id="mobile-nav-overlay"></div>
<div id="mobile-nav-drawer">
  <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
    <a href="/" class="flex items-center gap-2">
      <div style="width:36px;height:36px;border-radius:50%;background:#913FE2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      </div>
      <span class="font-semibold text-lg" style="background:linear-gradient(to right,#913FE2,#A78BFA);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= SITE_NAME ?></span>
    </a>
    <button id="mobile-nav-close" class="p-2 rounded-full" style="background:rgba(255,255,255,.06);color:#fff;border:none;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px">×</button>
  </div>
  <nav class="flex flex-col gap-1 p-3">
    <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $curPage==='home' ? 'bg-[#913FE2] text-white' : 'text-white/80 hover:bg-white/5 hover:text-white' ?> transition-colors">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span class="font-medium">Home</span>
    </a>
    <a href="/browse" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $curPage==='browse' ? 'bg-[#913FE2] text-white' : 'text-white/80 hover:bg-white/5 hover:text-white' ?> transition-colors">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <span class="font-medium">Browse</span>
    </a>
  </nav>
  <div class="px-4 py-3 border-t border-white/10 mt-auto">
    <form action="/browse" method="get">
      <div class="relative">
        <input type="search" name="q" placeholder="Search…" value="<?= h($_GET['q'] ?? '') ?>" autocomplete="off"
               style="width:100%;background:#13111A;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.6rem 1rem .6rem 2.5rem;color:#fff;font-size:.9rem;outline:none">
        <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4)" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
    </form>
  </div>
</div>

<!-- ═══ HEADER ═══ -->
<header class="asura-site-header relative z-50 font-medium" style="background:#913FE2;height:3.5rem;position:sticky;top:0">
  <div class="asura-header-inner mx-auto h-full w-full max-w-[1285px] px-3">
    <div class="flex items-center justify-between h-full">

      <!-- Left: Logo + Desktop Nav -->
      <div class="flex items-center gap-4 h-full">
        <!-- Logo -->
        <a href="/" aria-label="<?= SITE_NAME ?> Home" class="flex items-center gap-2 group">
          <div class="flex-shrink-0" style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;transition:transform .2s">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <span class="font-bold text-white text-lg hidden sm:block"><?= SITE_NAME ?></span>
        </a>

        <!-- Desktop Nav -->
        <nav class="asura-desktop-flex items-center gap-1 h-full">
          <a href="/" class="flex items-center justify-center px-3 py-2 rounded-md transition-colors hover:bg-black/20 <?= $curPage==='home' ? 'text-white font-semibold' : 'text-white/90 hover:text-white' ?>">
            <div>Home</div>
          </a>
          <a href="/browse" class="flex items-center justify-center px-3 py-2 rounded-md transition-colors hover:bg-black/20 <?= $curPage==='browse' ? 'text-white font-semibold' : 'text-white/90 hover:text-white' ?>">
            <div>Browse</div>
          </a>
        </nav>
      </div>

      <!-- Right: Search + Mobile btn -->
      <div class="flex items-center gap-2">
        <!-- Desktop Search -->
        <div class="asura-desktop-block">
          <form action="/browse" method="get" role="search">
            <div class="relative flex items-center">
              <input type="search" name="q" placeholder="Search manga, manhwa…"
                     value="<?= h($_GET['q'] ?? '') ?>" autocomplete="off"
                     style="background:rgba(0,0,0,.25);border:none;border-radius:8px;padding:.45rem 1rem .45rem 2.4rem;color:#fff;font-size:.875rem;width:220px;outline:none;transition:width .2s"
                     onfocus="this.style.width='280px'" onblur="this.style.width='220px'"
                     class="placeholder:text-white/60">
              <svg style="position:absolute;left:.7rem;color:rgba(255,255,255,.7)" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
          </form>
        </div>

        <!-- Mobile: Search icon + Hamburger -->
        <a href="/browse" class="asura-mobile-button p-2 rounded-md text-white/90 hover:text-white hover:bg-black/20 transition-colors" aria-label="Search">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </a>
        <button id="hamburger" class="asura-mobile-button p-2 rounded-md text-white/90 hover:text-white hover:bg-black/20 transition-colors" aria-label="Open menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>

    </div>
  </div>
</header>

<!-- ═══ MAIN ═══ -->
<main class="flex-1">
<?= $content ?>
</main>

<!-- ═══ FOOTER ═══ -->
<footer class="mt-auto">
  <div style="background:#1D1B22">
    <div class="mx-auto px-3 max-w-[1285px]">
      <div class="py-5">
        <div class="flex flex-col items-center gap-4 text-center">
          <!-- Logo -->
          <a href="/" class="flex items-center gap-3">
            <div style="width:44px;height:44px;border-radius:50%;background:#913FE2;display:flex;align-items:center;justify-content:center">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <span class="font-semibold text-xl" style="background:linear-gradient(to right,#913FE2,#A78BFA);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= SITE_NAME ?></span>
          </a>
          <!-- Footer links -->
          <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 font-medium text-sm">
            <a href="/"        class="text-white/70 hover:text-white transition-colors">Home</a>
            <a href="/browse"  class="text-white/70 hover:text-white transition-colors">Browse</a>
          </div>
        </div>
      </div>
    </div>
    <div style="height:1px;background:rgba(255,255,255,.1)"></div>
    <div class="flex-center opacity-60 text-xs py-3 max-w-[1285px] mx-auto px-3">
      &copy; <?= date('Y') ?> <strong><?= SITE_NAME ?></strong>. All rights reserved. All manga titles belong to their respective authors.
    </div>
  </div>
</footer>

<div id="toast" role="status" aria-live="polite"></div>

<script src="/public/js/app.js"></script>
</body>
</html>
