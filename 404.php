<?php
require_once __DIR__ . '/includes/functions.php';
http_response_code(404);
$curPage   = '';
$pageTitle = '404 — Page Not Found';
ob_start();
?>
<div class="flex flex-col items-center justify-center py-24 text-center px-4 min-h-[60vh]">
  <div class="text-8xl font-bold mb-4" style="background:linear-gradient(to right,#913FE2,#A78BFA);-webkit-background-clip:text;-webkit-text-fill-color:transparent">404</div>
  <h1 class="text-2xl font-semibold text-white mb-3">Page Not Found</h1>
  <p class="text-white/50 max-w-md mb-8">The page you're looking for doesn't exist or may have been moved.</p>
  <div class="flex gap-3 flex-wrap justify-center">
    <a href="<?= SITE_URL ?>/" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white border border-white/20 hover:bg-white/10 transition-colors" style="background:#2b2931">← Home</a>
    <a href="<?= SITE_URL ?>/browse" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white transition-colors" style="background:#913FE2">Browse Manga</a>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
