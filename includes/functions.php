<?php
declare(strict_types=1);

define('SITE_NAME', 'AsuraManga2');
define('DATA_FILE',  __DIR__ . '/../data/comics.json');

// Auto-detect base URL — works on localhost, shared hosting, VPS, subdirectory
(function (): void {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // SCRIPT_NAME: e.g. /asuramanga2/index.php  →  base = /asuramanga2
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base   = rtrim(dirname($script), '/\\');
    // When running via router.php the dirname points to project root correctly
    define('SITE_URL', $scheme . '://' . $host . $base);
})();

/* ── Data ────────────────────────────────── */

function loadComics(): array
{
    static $comics = null;
    if ($comics === null) {
        $raw    = file_get_contents(DATA_FILE);
        $comics = json_decode($raw, true) ?? [];
    }
    return $comics;
}

function getComic(string $slug): ?array
{
    foreach (loadComics() as $c) {
        if ($c['slug'] === $slug) return $c;
    }
    return null;
}

function searchComics(string $q): array
{
    $q = strtolower(trim($q));
    if ($q === '') return loadComics();
    return array_values(array_filter(loadComics(), fn($c) =>
        str_contains(strtolower($c['title']),       $q) ||
        str_contains(strtolower($c['description']), $q) ||
        str_contains(strtolower($c['alternateNames'] ?? ''), $q)
    ));
}

function filterComics(string $genre, string $status, string $sort): array
{
    $comics = loadComics();

    if ($genre !== '') {
        $comics = array_values(array_filter($comics,
            fn($c) => in_array($genre, $c['genres'] ?? [], true)));
    }
    if ($status !== '') {
        $comics = array_values(array_filter($comics,
            fn($c) => strcasecmp($c['status'] ?? 'ongoing', $status) === 0));
    }

    usort($comics, match ($sort) {
        'rating'   => fn($a,$b) => floatval($b['rating'])     <=> floatval($a['rating']),
        'chapters' => fn($a,$b) => intval($b['episodeCount']) <=> intval($a['episodeCount']),
        'updated'  => fn($a,$b) => strcmp($b['lastUpdated'],    $a['lastUpdated']),
        default    => fn($a,$b) => strcmp($a['title'],          $b['title']),
    });

    return $comics;
}

/* ── Top by period (Day / Week / Month) ──── */

/**
 * Returns top comics updated within a time period, sorted by rating.
 * Uses the pre-computed latestTs (Unix timestamp of most recent chapter).
 * Period: 'day' | 'week' | 'month' | 'all'
 */
function getTopByPeriod(string $period, int $limit = 12): array
{
    // Data was scraped on May 11 2026 — use max latestTs as "now"
    $comics = loadComics();
    $scrapeNow = 0;
    foreach ($comics as $c) {
        if (($c['latestTs'] ?? 0) > $scrapeNow) $scrapeNow = $c['latestTs'];
    }
    if ($scrapeNow === 0) $scrapeNow = time();

    $cutoff = match ($period) {
        'day'   => $scrapeNow - 86400,
        'week'  => $scrapeNow - 7  * 86400,
        'month' => $scrapeNow - 30 * 86400,
        default => 0,   // 'all' — no cutoff
    };

    $filtered = array_values(array_filter($comics,
        fn($c) => ($c['latestTs'] ?? 0) >= $cutoff && !empty($c['chapters'])
    ));

    usort($filtered, fn($a, $b) => floatval($b['rating']) <=> floatval($a['rating']));
    return array_slice($filtered, 0, $limit);
}

/**
 * Popular comics sorted by viewCount, then rating.
 * Period: 'week' | 'month' | 'all'
 */
function getPopular(int $limit = 10, string $period = 'week'): array
{
    $comics = loadComics();
    if ($period !== 'all') {
        $scrapeNow = 0;
        foreach ($comics as $c) {
            if (($c['latestTs'] ?? 0) > $scrapeNow) $scrapeNow = $c['latestTs'];
        }
        if ($scrapeNow === 0) $scrapeNow = time();
        $cutoff = $period === 'week' ? $scrapeNow - 7 * 86400 : $scrapeNow - 30 * 86400;
        $comics = array_values(array_filter($comics, fn($c) => ($c['latestTs'] ?? 0) >= $cutoff));
    }
    // Sort: viewCount DESC, then rating DESC
    usort($comics, function ($a, $b) {
        $vc = ($b['viewCount'] ?? 0) <=> ($a['viewCount'] ?? 0);
        if ($vc !== 0) return $vc;
        return floatval($b['rating']) <=> floatval($a['rating']);
    });
    return array_slice(array_values(array_filter($comics, fn($c) => !empty($c['chapters']))), 0, $limit);
}

/**
 * Latest updates: comics sorted by latestTs descending.
 */
function getLatestUpdates(int $limit = 12): array
{
    $comics = loadComics();
    usort($comics, fn($a, $b) => ($b['latestTs'] ?? 0) <=> ($a['latestTs'] ?? 0));
    return array_slice(array_filter($comics, fn($c) => !empty($c['chapters'])), 0, $limit);
}

function allGenres(): array
{
    $counts = [];
    foreach (loadComics() as $c) {
        foreach ($c['genres'] ?? [] as $g) {
            $counts[$g] = ($counts[$g] ?? 0) + 1;
        }
    }
    arsort($counts);
    return array_keys($counts);
}

/* ── URL helpers ─────────────────────────── */

function comicUrl(array $c): string
{
    return SITE_URL . '/comic/' . rawurlencode($c['slug']);
}

function chapterUrl(array $c, int $num): string
{
    return SITE_URL . '/reader/' . rawurlencode($c['slug']) . '/' . $num;
}

function coverUrl(array $c): string
{
    $cover = $c['cover'] ?? '';
    return str_starts_with($cover, '/') ? SITE_URL . $cover : $cover;
}

function browseUrl(array $override = []): string
{
    $params = array_merge([
        'q'      => $_GET['q']      ?? '',
        'genre'  => $_GET['genre']  ?? '',
        'status' => $_GET['status'] ?? '',
        'sort'   => $_GET['sort']   ?? '',
        'page'   => $_GET['page']   ?? 1,
    ], $override);

    $params = array_filter($params, fn($v) => $v !== '' && $v != 1);
    $qs     = $params ? '?' . http_build_query($params) : '';
    return SITE_URL . '/browse' . $qs;
}

/* ── Template helpers ───────────────────── */

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatDate(string $iso): string
{
    if ($iso === '' || str_starts_with($iso, '0001')) return '';
    try {
        return (new DateTime($iso))->format('M d, Y');
    } catch (Exception) {
        return $iso;
    }
}

function paginate(array $items, int $perPage, int $page): array
{
    $total = count($items);
    $pages = max(1, (int) ceil($total / $perPage));
    $page  = max(1, min($page, $pages));
    return [
        'items' => array_slice($items, ($page - 1) * $perPage, $perPage),
        'total' => $total,
        'pages' => $pages,
        'page'  => $page,
    ];
}

function renderPagination(int $page, int $pages, callable $urlFn): string
{
    if ($pages <= 1) return '';
    $html = '<div class="pagination">';

    if ($page > 1) $html .= '<a href="' . $urlFn($page - 1) . '" class="page-btn">‹</a>';

    $range = range(max(1, $page - 2), min($pages, $page + 2));
    if (!in_array(1, $range)) {
        $html .= '<a href="' . $urlFn(1) . '" class="page-btn">1</a>';
        if ($range[0] > 2) $html .= '<span class="page-dots">…</span>';
    }
    foreach ($range as $p) {
        $active = $p === $page ? ' active' : '';
        $html .= '<a href="' . $urlFn($p) . '" class="page-btn' . $active . '">' . $p . '</a>';
    }
    if (!in_array($pages, $range)) {
        if ($range[count($range)-1] < $pages - 1) $html .= '<span class="page-dots">…</span>';
        $html .= '<a href="' . $urlFn($pages) . '" class="page-btn">' . $pages . '</a>';
    }

    if ($page < $pages) $html .= '<a href="' . $urlFn($page + 1) . '" class="page-btn">›</a>';
    $html .= '</div>';
    return $html;
}
