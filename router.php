<?php
/**
 * PHP built-in server router
 * php -S localhost:8080 router.php
 */

$root = __DIR__;
$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// ── Static file serving (CSS, JS, images, fonts) ──
$staticFile = $root . $uri;
if ($uri !== '/' && is_file($staticFile)) {
    $ext  = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    $mime = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'avif'  => 'image/avif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'txt'   => 'text/plain',
        'xml'   => 'application/xml',
    ][$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    readfile($staticFile);
    return;
}

// ── PHP routing ──
if ($uri === '/' || $uri === '/index.php') {
    require $root . '/index.php';

} elseif (preg_match('#^/comic/([^/?]+)$#', $uri, $m)) {
    $_GET['slug'] = urldecode($m[1]);
    require $root . '/comic.php';

} elseif (preg_match('#^/reader/([^/?]+)/(\d+)$#', $uri, $m)) {
    $_GET['slug']    = urldecode($m[1]);
    $_GET['chapter'] = (int)$m[2];
    require $root . '/reader.php';

} elseif (preg_match('#^/browse$#', $uri)) {
    require $root . '/browse.php';

} else {
    http_response_code(404);
    require $root . '/404.php';
}
