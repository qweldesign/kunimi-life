<?php
/**
 * people.php
 *
 * この町の人 (一覧: /people/, 個別記事: /people/xx/) と, トップページのカード
 * 記事は content/people/*.md (frontmatter: slug, catch, name, summary, img, website, note, instagram)
 * slug が連番 (01, 02, …) を兼ね, 一覧は slug 順に並べる
 */

require_once dirname(__DIR__) . '/partials/site.php';

// 記事のタイトル (<title> とパンくず用. 「vol.NN キャッチ ― 氏名」)
function person_title(array $post): string {
  $title = implode(' ― ', array_filter([$post['catch'] ?? '', $post['name'] ?? ''], 'strlen'));
  return 'vol.' . ($post['slug'] ?? '') . ' ' . $title;
}

// 人物の外部リンク (WEB / note / Instagram)
function render_person_links(array $post): void {
  $links = [
    'website'   => ['label' => 'WEB',       'icon' => 'icon_globe.png'],
    'note'      => ['label' => 'note',      'icon' => 'icon_note.png'],
    'instagram' => ['label' => 'Instagram', 'icon' => 'icon_instagram.png'],
  ];
?>
                <ul class="personLinks">
<?php foreach ($links as $key => $link) { ?>
<?php if (!empty($post[$key])) { ?>
                  <li class="personLinks__item">
                    <a href="<?= e($post[$key]) ?>" target="_blank" rel="noopener">
                      <img src="<?= path('assets/' . $link['icon']) ?>" alt="" width="20" height="20">
                      <span><?= e($link['label']) ?></span>
                    </a>
                  </li>
<?php } ?>
<?php } ?>
                </ul>
<?php
}

/**
 * 人物のカード (.personCard)
 * $options:
 *   heading: キャッチの見出しのタグ (規定で h2. トップページでは h3)
 *   links: 外部リンクを付けるか (規定で true)
 */
function render_person_card(array $post, array $options = []): void {
  $tag = $options['heading'] ?? 'h2';
?>
              <li class="personCard">
                <a class="personCard__link" href="<?= e(post_url('people', $post)) ?>">
                  <figure class="personCard__image">
                    <img loading="lazy" src="<?= e(image_url($post['img'] ?? null)) ?>" alt="<?= e($post['name'] ?? '') ?>">
                  </figure>
                  <div class="personCard__body">
                    <p class="personCard__vol">vol.<?= e($post['slug'] ?? '') ?></p>
                    <<?= $tag ?> class="personCard__catch"><?= e($post['catch'] ?? '') ?></<?= $tag ?>>
                    <p class="personCard__name"><?= e($post['name'] ?? '') ?></p>
                    <p class="personCard__role"><?= e($post['summary'] ?? '') ?></p>
                  </div>
                </a>
<?php if ($options['links'] ?? true) render_person_links($post); ?>
              </li>
<?php
}

// 一覧と個別記事
function render_people(): void {
  $type   = 'people';
  $config = post_type($type);
  $label  = $config['label'];
  $cms    = content_engine($type);

  // 記事が見つからない場合と, 一覧のページ番号が範囲外の場合は 404
  $notFound = $cms->is_not_found();
  if ($notFound) {
    http_response_code(404);
  }

  // 記事の frontmatter (見つからなければ空)
  $article = [];
  if ($cms->is_found()) {
    foreach (['slug', 'catch', 'name', 'summary', 'img', 'website', 'note', 'instagram'] as $key) {
      $article[$key] = $cms->get_meta($key);
    }
  }

  if ($cms->is_found()) {
    $page = [
      'title'       => person_title($article) . ' | ' . $label,
      'description' => $article['name'] . '（' . $article['summary'] . '）のインタビュー。' . $article['catch'],
      'path'        => post_path($type, $article),
      'image'       => $article['img'],
    ];
  } else {
    $pageNumber = $cms->get_page();
    $page = [
      'title'       => $notFound ? 'ページが見つかりません' : ($pageNumber > 1 ? "{$label} ({$pageNumber}ページ目)" : $label),
      'description' => $config['description'],
      'path'        => page_path($type, $pageNumber),
      'type'        => 'website',
      'noindex'     => $notFound,
    ];
  }

  // 個別記事では見出しを一覧へのリンクにする
  $page['header'] = $cms->is_found()
    ? fn() => render_page_header($label, type_url($type))
    : fn() => render_page_header($notFound ? 'ページが見つかりません' : $label, null, $notFound ? '' : $config['lead']);

  // パンくず (相談室 > この町の人 > vol.NN …)
  $breadcrumb = new ContentNavigation([
    'parents'     => array_map(fn($crumb) => ['label' => $crumb['label'], 'path' => path($crumb['path'])], SITE_BREADCRUMB),
    'subdirLabel' => $label,
    'subdirPath'  => type_url($type),
  ]);

  render_header($page);
?>
    <main id="main" class="main">
      <div class="main__container">
        <?= $cms->is_found() ? $breadcrumb->breadcrumb(['title' => person_title($article)]) : $breadcrumb->breadcrumb() ?>
<?php if ($notFound) { ?>
        <p>お探しのページは、削除されたか、URLが変更された可能性があります。</p>
        <p><a href="<?= e(type_url($type)) ?>"><?= e($label) ?>の一覧へ</a></p>
<?php } elseif (!$cms->is_found()) { ?>
        <ul class="personGrid">
<?php foreach ($cms->get_posts() as $post) render_person_card($post); ?>
        </ul>
        <?= $cms->pagination() ?>
<?php } else { ?>
        <div class="main__row">
          <article class="main__article peopleArticle">
<?= $cms->get_content() ?>
<?php if ($article['website'] || $article['note'] || $article['instagram']) { ?>
            <section class="peopleArticle__links">
              <h3 class="peopleArticle__linksTitle"><?= e($article['name'] ?: 'この方') ?>の活動をもっと見る</h3>
<?php render_person_links($article); ?>
            </section>
<?php } ?>
<?php $adjacent = $cms->get_adjacent(); ?>
            <nav class="peopleArticle__pager" aria-label="前後の記事">
<?php foreach (['prev' => '前の記事', 'next' => '次の記事'] as $key => $pagerLabel) { ?>
<?php if ($adjacent[$key]) { ?>
              <a class="peopleArticle__pagerItem is-<?= $key ?>" href="<?= e(post_url($type, $adjacent[$key])) ?>">
                <span class="peopleArticle__pagerLabel"><?= e($pagerLabel) ?></span>
                <span class="peopleArticle__pagerName">vol.<?= e($adjacent[$key]['slug'] ?? '') ?> <?= e($adjacent[$key]['name'] ?? '') ?></span>
              </a>
<?php } else { ?>
              <span class="peopleArticle__pagerItem is-<?= $key ?> is-empty"></span>
<?php } ?>
<?php } ?>
            </nav>
            <aside class="peopleArticle__cta">
              <p class="peopleArticle__ctaLead">国見の暮らしを、実際に歩いて確かめてみませんか？</p>
              <a class="peopleArticle__ctaButton" href="<?= e(path()) ?>#banner">空き家ツアーのご案内</a>
            </aside>
          </article>
          <aside class="main__aside">
            <h2 class="postIndex__heading"><?= e($label) ?></h2>
            <ul class="postIndex">
<?php foreach ($cms->get_posts(1, 10) as $post) { ?>
<?php $current = ($post['slug'] ?? '') === $article['slug']; ?>
              <li class="postIndex__item<?= $current ? ' is-current' : '' ?>">
                <a href="<?= e(post_url($type, $post)) ?>"<?= $current ? ' aria-current="page"' : '' ?>>
                  <img loading="lazy" class="postIndex__image" src="<?= e(image_url($post['img'] ?? null)) ?>" alt="">
                  <span class="postIndex__text">
                    <span class="postIndex__vol">vol.<?= e($post['slug'] ?? '') ?></span>
                    <span class="postIndex__title"><?= e($post['name'] ?? '') ?></span>
                    <span class="postIndex__summary"><?= e($post['summary'] ?? '') ?></span>
                  </span>
                </a>
              </li>
<?php } ?>
            </ul>
          </aside>
        </div>
<?php } ?>
      </div>
    </main>
<?php
  render_footer();
}
