<?php
/**
 * site.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * 全ページ共通のヘッダー・フッターを出力する
 * 中身 (サイト名, ナビ, SNS など) は config.php の設定から作る
 */

require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/helpers.php';

/**
 * <head> ~ ヘッダー・ナビまで出力
 * $page:
 *   title: ページのタイトル (<title> は「title | サイト名」. 省略するとサイト名だけ)
 *   full_title: <title> をそのまま指定する (トップページなど)
 *   description: description (規定で SITE_DESCRIPTION)
 *   path: このページのサイトのルートからのパス (canonical, og:url に使う)
 *   image: OGP 画像 (規定で SITE_OGP_IMAGE)
 *   image_alt, image_width, image_height: OGP 画像の代替テキストと大きさ
 *   type: og:type (規定で article)
 *   noindex: 検索結果に出さない (404 など)
 *   home: トップページか (ナビをページ内リンクにする)
 *   head: </head> の前に足す HTML (トップページのコンバージョン計測など)
 *   header: ナビの前に出すヘッダー (関数. トップページと下層ページで中身が違う)
 */
function render_header(array $page = []): void {
  $title = $page['full_title'] ?? (!empty($page['title']) ? $page['title'] . ' | ' . SITE_NAME : SITE_NAME);
  $description = ($page['description'] ?? '') ?: SITE_DESCRIPTION;
  $url = absolute_url($page['path'] ?? '');
  $image = absolute_url(($page['image'] ?? '') ?: SITE_OGP_IMAGE);
  $home = !empty($page['home']);
  ?>
<!DOCTYPE html>
<html lang="<?= e(SITE_LANG) ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--
      Built with QWEL Markdown Engine (Simple CMS)
      A lightweight, file-based CMS that renders Markdown as HTML.
      Created by QWEL.DESIGN - https://qwel.design
      Source: https://github.com/qweldesign/qwel-tools-md-engine (MIT License)
    -->
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
<?php if (!empty($page['noindex'])) { ?>
    <meta name="robots" content="noindex">
<?php } else { ?>
    <link rel="canonical" href="<?= e($url) ?>">
<?php } ?>
    <meta property="og:type" content="<?= e($page['type'] ?? 'article') ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($url) ?>">
    <meta property="og:image" content="<?= e($image) ?>">
<?php if (!empty($page['image_width'])) { ?>
    <meta property="og:image:width" content="<?= e((string)$page['image_width']) ?>">
    <meta property="og:image:height" content="<?= e((string)$page['image_height']) ?>">
<?php } ?>
<?php if (!empty($page['image_alt'])) { ?>
    <meta property="og:image:alt" content="<?= e($page['image_alt']) ?>">
<?php } ?>
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary_large_image">
<?php if (SITE_FONTS_URL) { ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= e(SITE_FONTS_URL) ?>">
<?php } ?>
    <link rel="stylesheet" href="<?= path('style-1.1.css') ?>">
    <link rel="icon" href="<?= path('assets/favicon.ico') ?>" sizes="32x32">
    <link rel="icon" href="<?= path('assets/favicon.svg') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= path('assets/apple-touch-icon.png') ?>">
<?php if (SITE_GA_ID) { ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(SITE_GA_ID) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?= e(SITE_GA_ID) ?>');
    </script>
<?php } ?>
<?php if (!empty($page['head'])) { ?>
<?= $page['head'] . "\n" ?>
<?php } ?>
  </head>
  <body>
<?php if (isset($page['header'])) $page['header'](); ?>
    <nav class="gNav hidden">
<?php if (SITE_NAV) { ?>
      <ul class="gNav__primaryMenu visible-md" data-primary-menu>
<?php foreach (SITE_NAV as $id => $label) { ?>
        <li class="gNav__menuItem">
          <a href="<?= $home ? '#' . e($id) : path() . '#' . e($id) ?>"><?= e($label) ?></a>
        </li>
<?php } ?>
      </ul>
<?php } ?>
<?php if (SITE_SNS) { ?>
      <ul class="gNav__socialMenu visible-md" data-social-menu>
<?php foreach (SITE_SNS as $sns) { ?>
<?php $external = (bool)preg_match('#^https?://#', $sns['url']); ?>
        <li class="gNav__menuItem is-social">
          <a href="<?= e(path($sns['url'])) ?>" aria-label="<?= e($sns['label']) ?>"<?= $external ? ' target="_blank" rel="noopener"' : '' ?>>
            <svg class="icon is-<?= e($sns['icon'] ?? '') ?> is-md" width="36" height="36" aria-hidden="true">
              <use href="<?= path('icons.svg') ?>#<?= e($sns['icon'] ?? '') ?>"></use>
            </svg>
          </a>
        </li>
<?php } ?>
      </ul>
<?php } ?>
    </nav>
<?php
}

// 下層ページのヘッダー (.pageHeader)
// $title: 見出し, $titleUrl: 見出しをリンクにする場合の URL (個別記事で一覧へ戻るリンクにする), $lead: 見出しの下の文
function render_page_header(string $title, ?string $titleUrl = null, string $lead = ''): void {
  ?>
    <header class="pageHeader">
      <div class="hidden" data-site-brand>
        <a href="<?= e(SITE_LOGO_LINK ?: path()) ?>">
          <img src="<?= path(SITE_LOGO) ?>" alt="<?= e(SITE_NAME) ?>">
        </a>
      </div>
      <p class="pageHeader__eyebrow"><a href="<?= e(path()) ?>"><?= e(SITE_NAME) ?></a></p>
<?php if ($titleUrl !== null) { ?>
      <p class="pageHeader__title"><a href="<?= e($titleUrl) ?>"><?= e($title) ?></a></p>
<?php } else { ?>
      <h1 class="pageHeader__title"><?= e($title) ?></h1>
<?php } ?>
<?php if ($lead !== '') { ?>
      <p class="pageHeader__lead"><?= e($lead) ?></p>
<?php } ?>
    </header>
<?php
}

// フッター ~ </html> まで出力
function render_footer(): void {
  ?>
    <footer id="footer" class="footer">
      <div class="footer__inner">
<?php if (SITE_CREDITS) { ?>
        <table class="footer__table">
<?php foreach (SITE_CREDITS as $label => $value) { ?>
          <tr>
            <td><?= e($label) ?>:</td>
            <td><?= e($value) ?></td>
          </tr>
<?php } ?>
        </table>
<?php } ?>
      </div>
    </footer>
    <script src="<?= path('init.js') ?>" type="module"></script>
  </body>
</html>
<?php
}
