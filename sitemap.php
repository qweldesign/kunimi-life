<?php
/**
 * サイトマップ (/sitemap.xml)
 * config.php の投稿タイプと content/ の Markdown から自動で作るので, 記事を足しても書き換えは要らない
 * /sitemap.xml へのアクセスは, ルートの .htaccess でここに振り分ける
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/ContentLoader.php';

$urls = [];
$latestAll = '';
$today = date('Y-m-d');

// 記事の更新日 (日付が未来の記事は, これから開催するツアーなどなので更新日にしない)
$lastmod = fn(array $post): string => ($post['date'] ?? '') <= $today ? ($post['date'] ?? '') : '';

foreach (array_keys(SITE_POST_TYPES) as $type) {
  $config = post_type($type);
  $posts = (new ContentLoader(__DIR__ . '/' . trim($config['dir'], '/')))->load();
  // 一覧の更新日は, 記事の更新日のうち最も新しいもの
  $latest = max(array_merge([''], array_map($lastmod, $posts)));
  if ($latest > $latestAll) $latestAll = $latest;

  if ($config['list']) {
    $urls[] = ['path' => type_path($type), 'date' => $latest];
  }
  foreach ($posts as $post) {
    $urls[] = ['path' => post_path($type, $post), 'date' => $lastmod($post)];
  }
}

// 投稿タイプ以外のページ (config.php の SITE_PAGES)
foreach (SITE_PAGES as $pagePath) {
  $urls[] = ['path' => $pagePath, 'date' => ''];
}

// トップページ (最新記事を載せているので, 全体の最新の日付にする)
array_unshift($urls, ['path' => '', 'date' => $latestAll]);

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url) { ?>
  <url>
    <loc><?= e(absolute_url($url['path'])) ?></loc>
<?php if ($url['date']) { ?>
    <lastmod><?= e(ContentEngine::format_date($url['date'], true)) ?></lastmod>
<?php } ?>
  </url>
<?php } ?>
</urlset>
