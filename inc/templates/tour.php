<?php
/**
 * tour.php
 *
 * ツアー (一覧: /tour/, 個別記事: /tour/xx/) と, トップページの開催概要 (#banner)
 * 記事は content/tour/*.md. 1回のツアー = 1記事で, frontmatter に開催要項, 本文に開催後の報告を書く
 * (開催前は本文を空にしておき, 開催後に報告を書き足す)
 *
 * frontmatter:
 *   slug: 回数 (01, 02, …. 「第N回」の表示にも使う)
 *   title: ツアー名 (例: 第3回 空き家マッチングツアー)
 *   date: 開催日 (2027-04-17)
 *   time: 時間 (10:00 - 16:00)
 *   deadline: 申込締切日 (任意. 書かない場合は開催日の終わりまで募集中)
 *   meeting, meeting_note: 集合場所とその補足
 *   fee, fee_note: 参加費とその補足
 *   price: 参加費の金額 (数字のみ. 任意. 構造化データに使う. 書かない場合は fee が「無料」なら 0 円とみなす)
 *   summary: 一覧やカード, description に使う要約
 *   img: アイキャッチ (サイトのルートからのパス)
 *   participants: 参加者数 (数字のみ. 開催後に書く)
 *   inquiries: ツアー後のお問い合わせ件数 (数字のみ. 開催後に書く)
 *   status: 状態を手動で決める場合に open / closed / done のいずれか (書かなければ日付から判定する)
 * ※ 値が空の項目は, キーごと書かない (frontmatter の読み込みの都合で, "" は空にならない)
 */

require_once dirname(__DIR__) . '/partials/site.php';

// 状態とバッジの表示
const TOUR_STATUSES = [
  'open'   => '参加者募集中',
  'closed' => '受付終了',
  'done'   => '開催済み',
];

// 実績の数値 (frontmatter のキー => 項目名と単位)
const TOUR_METRICS = [
  'participants' => ['label' => '参加者', 'unit' => '名'],
  'inquiries'    => ['label' => '事後のお問い合わせ', 'unit' => '件'],
];

/**
 * ツアーの状態 (open: 募集中 / closed: 受付終了 / done: 開催済み)
 * status の指定があればそれを使い, なければ締切日・開催日と現在の日時から判定する
 */
function tour_status(array $tour, ?DateTimeImmutable $now = null): string {
  $override = $tour['status'] ?? '';
  if (isset(TOUR_STATUSES[$override])) return $override;
  if (empty($tour['date'])) return 'open';

  $now ??= new DateTimeImmutable();
  $eventEnd = new DateTimeImmutable($tour['date'] . ' 23:59:59');
  if ($now > $eventEnd) return 'done';

  $deadline = !empty($tour['deadline']) ? new DateTimeImmutable($tour['deadline'] . ' 23:59:59') : $eventEnd;
  return $now > $deadline ? 'closed' : 'open';
}

// 開催済みか
function tour_is_done(array $tour): bool {
  return tour_status($tour) === 'done';
}

// 報告 (本文) が書かれているか
function tour_has_report(array $tour): bool {
  return trim($tour['content'] ?? '') !== '';
}

// 「第N回」 (slug の数字から作る)
function tour_number(array $tour): string {
  return ctype_digit($tour['slug'] ?? '') ? '第' . (int)$tour['slug'] . '回' : '';
}

// ツアー名 (title がなければ「第N回 ツアー」)
function tour_title(array $tour): string {
  return ($tour['title'] ?? '') ?: trim(tour_number($tour) . ' ' . post_type('tour')['label']);
}

/**
 * トップページのカードに出すツアー
 * これから開催するツアー (募集中・受付終了) のうち開催日の近いもの. なければ最新の開催済みツアー (次回が未定の間)
 */
function tour_featured(array $tours): ?array {
  $upcoming = array_values(array_filter($tours, fn($tour) => !tour_is_done($tour)));
  usort($upcoming, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
  return $upcoming[0] ?? $tours[0] ?? null; // $tours は開催日の新しい順
}

// 実績の数値 (書かれているものだけ)
function tour_metrics(array $tour): array {
  $metrics = [];
  foreach (TOUR_METRICS as $key => $metric) {
    if (($tour[$key] ?? '') !== '') $metrics[] = $metric + ['value' => $tour[$key]];
  }
  return $metrics;
}

// 開催日の time 要素 (例: 2026.09.27 SUN)
function tour_date_tag(array $tour, string $class): string {
  if (empty($tour['date'])) return '';
  $time = strtotime($tour['date']);
  return '<time class="' . e($class) . '" datetime="' . e(date('Y-m-d', $time)) . '">'
    . e(date('Y.m.d', $time)) . ' ' . e(strtoupper(date('D', $time))) . '</time>';
}

// 状態バッジ
function render_tour_badge(string $status, string $block): void {
?>
<span class="<?= $block ?>__badge is-<?= e($status) ?>"><?= e(TOUR_STATUSES[$status]) ?></span>
<?php
}

// 実績の数値 (.tourMetrics)
function render_tour_metrics(array $tour, string $modifier = ''): void {
  $metrics = tour_metrics($tour);
  if (!$metrics) return;
?>
            <dl class="tourMetrics<?= $modifier !== '' ? ' tourMetrics--' . e($modifier) : '' ?>">
<?php foreach ($metrics as $metric) { ?>
              <div class="tourMetrics__item">
                <dt class="tourMetrics__label"><?= e($metric['label']) ?></dt>
                <dd class="tourMetrics__value"><span class="tourMetrics__number"><?= e($metric['value']) ?></span><?= e($metric['unit']) ?></dd>
              </div>
<?php } ?>
            </dl>
<?php
}

// 開催要項 (集合・参加費・お問い合わせ)
function render_tour_details(array $tour, string $block): void {
  $contact = SITE_TOUR['contact'];
?>
            <dl class="<?= $block ?>__list">
<?php if (!empty($tour['meeting'])) { ?>
              <div class="<?= $block ?>__listItem">
                <dt>集合</dt>
                <dd><?= e($tour['meeting']) ?><?php if (!empty($tour['meeting_note'])) { ?><small class="is-block"><?= e($tour['meeting_note']) ?></small><?php } ?></dd>
              </div>
<?php } ?>
<?php if (!empty($tour['fee'])) { ?>
              <div class="<?= $block ?>__listItem">
                <dt>参加費</dt>
                <dd><?= e($tour['fee']) ?><?php if (!empty($tour['fee_note'])) { ?><small> (<?= e($tour['fee_note']) ?>)</small><?php } ?></dd>
              </div>
<?php } ?>
              <div class="<?= $block ?>__listItem">
                <dt>お問い合わせ</dt>
                <dd><a href="tel:<?= e($contact['tel']) ?>"><?= e($contact['label']) ?></a><?php if (!empty($contact['note'])) { ?><small> (<?= e($contact['note']) ?>)</small><?php } ?></dd>
              </div>
            </dl>
<?php
}

/**
 * トップページのツアーのセクション (#banner)
 * 次に開催するツアーの開催概要を大きなカードで出し, 下に開催報告の一覧 (/tour/) へのボタンを置く
 * 次回が未定の間は, 最新の開催済みツアーの実績を出して LINE のお知らせへ誘導する
 */
function render_tour_banner(): void {
  $tours = content_engine('tour', ['slug' => null, 'page' => 1])->get_posts(1, PHP_INT_MAX);
  $tour  = tour_featured($tours);
  if (!$tour) return;
  $status   = tour_status($tour);
  $time     = !empty($tour['date']) ? strtotime($tour['date']) : null;
  $hasDone  = (bool)array_filter($tours, 'tour_is_done');
  $entryUrl = path(SITE_TOUR['entry_url']);
?>
      <section id="banner" class="banner">
        <div class="banner__card is-<?= e($status) ?>">
          <p class="banner__status"><?php render_tour_badge($status, 'banner'); ?></p>
          <h2 class="banner__title">
            <span class="banner__eyebrow"><?= e(SITE_TOUR['area']) ?></span>
            <span class="banner__name"><?= e(tour_title($tour)) ?></span>
          </h2>
<?php if ($time) { ?>
          <p class="banner__date">
            <time class="banner__dateMain" datetime="<?= e(date('Y-m-d', $time)) ?>"><?= e(date('n.j', $time)) ?></time>
            <span class="banner__dateSub"><?= e(date('Y', $time)) ?> / <?= e(strtoupper(date('D', $time))) ?><?php if (!empty($tour['time'])) { ?><br><?= e($tour['time']) ?><?php } ?></span>
          </p>
<?php } ?>
<?php if ($status === 'done') { ?>
<?php render_tour_metrics($tour, 'banner'); ?>
          <div class="banner__actions">
<?php if (tour_has_report($tour)) { ?>
            <a class="banner__button is-report" href="<?= e(post_url('tour', $tour)) ?>">開催報告を読む</a>
<?php } ?>
            <p class="banner__next">次回の開催は決まり次第お知らせします。</p>
            <a class="banner__link" href="<?= e(SITE_TOUR['line_url']) ?>" target="_blank" rel="noopener">LINEでお知らせを受け取る</a>
          </div>
<?php } else { ?>
<?php render_tour_details($tour, 'banner'); ?>
          <div class="banner__actions">
<?php if ($status === 'open') { ?>
            <a class="banner__button" href="<?= e($entryUrl) ?>" onclick="return gtag_report_conversion('<?= e($entryUrl) ?>');">参加を申し込む</a>
<?php } ?>
            <a class="banner__link" href="#flow">ツアーの流れを見る</a>
          </div>
<?php } ?>
        </div>
<?php if ($hasDone) { ?>
        <div class="banner__buttonWrapper">
          <a class="banner__more" href="<?= e(type_url('tour')) ?>">これまでの開催報告を見る</a>
        </div>
<?php } ?>
      </section>
<?php
}

/**
 * ツアーのカード (.tourCard). /tour/ の一覧で使う
 */
function render_tour_card(array $tour): void {
  $status = tour_status($tour);
  // 開催前のツアーは要項が分かるトップページへ, 開催済みは報告があれば報告へ
  $url = $status !== 'done' ? path() . '#banner' : (tour_has_report($tour) ? post_url('tour', $tour) : null);
  $more = $status !== 'done' ? '開催概要を見る' : ($url ? '開催報告を読む' : '');
?>
          <li class="tourCard is-<?= e($status) ?>">
            <<?= $url ? 'a' : 'div' ?> class="tourCard__body"<?= $url ? ' href="' . e($url) . '"' : '' ?>>
              <figure class="tourCard__image">
                <img loading="lazy" src="<?= e(image_url($tour['img'] ?? null)) ?>" alt="">
              </figure>
              <div class="tourCard__text">
                <p class="tourCard__status"><?php render_tour_badge($status, 'tourCard'); ?></p>
                <h2 class="tourCard__heading"><?= e(tour_title($tour)) ?></h2>
                <p class="tourCard__meta"><?= tour_date_tag($tour, 'tourCard__time') ?></p>
<?php render_tour_metrics($tour); ?>
<?php if (!empty($tour['summary'])) { ?>
                <p class="tourCard__summary"><?= e($tour['summary']) ?></p>
<?php } ?>
<?php if ($more !== '') { ?>
                <span class="tourCard__more"><?= e($more) ?></span>
<?php } ?>
              </div>
            </<?= $url ? 'a' : 'div' ?>>
          </li>
<?php
}

/**
 * トップページのイベントの構造化データ (schema.org/Event の JSON-LD)
 * #banner と同じツアー (次に開催するツアー) を出す. 開催済みのツアーしかない間は出さない
 * 戻り値は render_header の head にそのまま渡せる <script> 要素 (出さない場合は空文字)
 */
function tour_event_json_ld(): string {
  $tours = content_engine('tour', ['slug' => null, 'page' => 1])->get_posts(1, PHP_INT_MAX);
  $tour  = tour_featured($tours);
  if (!$tour || empty($tour['date'])) return '';
  $status = tour_status($tour);
  if ($status === 'done') return '';

  // 時間 (例: 10:00 - 16:00) があれば日時に, なければ日付のみ
  $startDate = $endDate = date('Y-m-d', strtotime($tour['date']));
  if (preg_match('/(\d{1,2}):(\d{2})\s*[-–~〜]\s*(\d{1,2}):(\d{2})/u', $tour['time'] ?? '', $m)) {
    $offset    = date('P', strtotime($tour['date']));
    $startDate = sprintf('%sT%02d:%02d:00%s', $startDate, $m[1], $m[2], $offset);
    $endDate   = sprintf('%sT%02d:%02d:00%s', $endDate, $m[3], $m[4], $offset);
  }

  $place = SITE_TOUR['place'];
  $data = [
    '@context'            => 'https://schema.org',
    '@type'               => 'Event',
    'name'                => tour_title($tour),
    'description'         => ($tour['summary'] ?? '') ?: SITE_DESCRIPTION,
    'startDate'           => $startDate,
    'endDate'             => $endDate,
    'eventStatus'         => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
    'location'            => [
      '@type'   => 'Place',
      'name'    => $place['name'],
      'address' => [
        '@type'           => 'PostalAddress',
        'postalCode'      => $place['postal_code'],
        'addressRegion'   => $place['region'],
        'addressLocality' => $place['locality'],
        'streetAddress'   => $place['street'],
        'addressCountry'  => 'JP',
      ],
    ],
    'image'               => [absolute_url(($tour['img'] ?? '') ?: SITE_OGP_IMAGE)],
    'organizer'           => [
      '@type' => 'Organization',
      'name'  => SITE_NAME,
      'url'   => SITE_URL,
    ],
  ];

  // 参加費 (金額が分かるときだけ). 受付終了なら売り切れ扱い
  $price = ($tour['price'] ?? '') !== '' ? $tour['price'] : (($tour['fee'] ?? '') === '無料' ? '0' : null);
  if ($price !== null) {
    $data['offers'] = [
      '@type'         => 'Offer',
      'url'           => absolute_url(SITE_TOUR['entry_url']),
      'price'         => $price,
      'priceCurrency' => 'JPY',
      'availability'  => $status === 'open' ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
    ];
  }

  $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_PRETTY_PRINT);
  return $json === false ? '' : '    <script type="application/ld+json">' . "\n" . $json . "\n" . '    </script>';
}

// 一覧と個別記事 (報告)
function render_tours(): void {
  $type   = 'tour';
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
    foreach (['slug', 'title', 'date', 'time', 'deadline', 'meeting', 'meeting_note', 'fee', 'fee_note', 'summary', 'img', 'participants', 'inquiries', 'status', 'content'] as $key) {
      $article[$key] = $cms->get_meta($key);
    }
  }

  if ($cms->is_found()) {
    $done = tour_is_done($article);
    $page = [
      'title'       => tour_title($article) . ($done ? ' 開催報告' : '') . ' | ' . $label,
      'description' => $article['summary'] ?: $config['description'],
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

  // パンくず (相談室 > 空き家マッチングツアー > 第N回 …)
  $breadcrumb = new ContentNavigation([
    'parents'     => array_map(fn($crumb) => ['label' => $crumb['label'], 'path' => path($crumb['path'])], SITE_BREADCRUMB),
    'subdirLabel' => $label,
    'subdirPath'  => type_url($type),
  ]);

  render_header($page);
?>
    <main id="main" class="main">
      <div class="main__container">
        <?= $cms->is_found() ? $breadcrumb->breadcrumb(['title' => tour_title($article)]) : $breadcrumb->breadcrumb() ?>
<?php if ($notFound) { ?>
        <p>お探しのページは、削除されたか、URLが変更された可能性があります。</p>
        <p><a href="<?= e(type_url($type)) ?>"><?= e($label) ?>の一覧へ</a></p>
<?php } elseif (!$cms->is_found()) { ?>
        <ul class="tourGrid">
<?php foreach ($cms->get_posts() as $post) render_tour_card($post); ?>
        </ul>
        <?= $cms->pagination() ?>
<?php } else { ?>
<?php $status = tour_status($article); ?>
        <div class="main__row">
          <article class="main__article tourArticle">
            <header class="tourArticle__header">
              <p class="tourArticle__status"><?php render_tour_badge($status, 'tourArticle'); ?></p>
              <h1 class="tourArticle__title"><?= e(tour_title($article)) ?><?= $status === 'done' ? '<span class="tourArticle__titleSub">開催報告</span>' : '' ?></h1>
              <p class="tourArticle__meta"><?= tour_date_tag($article, 'tourArticle__time') ?><?= !empty($article['time']) ? ' ' . e($article['time']) : '' ?></p>
            </header>
<?php if (!empty($article['img'])) { ?>
            <figure class="tourArticle__image">
              <img src="<?= e(image_url($article['img'])) ?>" alt="">
            </figure>
<?php } ?>
<?php render_tour_metrics($article, 'article'); ?>
<?php if (tour_has_report($article)) { ?>
            <div class="tourArticle__body">
<?= $cms->get_content() ?>
            </div>
<?php } elseif ($status === 'done') { ?>
            <p class="tourArticle__empty">開催報告は準備中です。</p>
<?php } ?>
<?php if ($status !== 'done') { ?>
            <section class="tourArticle__details">
              <h2 class="tourArticle__detailsTitle">開催概要</h2>
<?php render_tour_details($article, 'tourArticle'); ?>
            </section>
<?php } ?>
            <aside class="tourArticle__cta">
<?php if ($status === 'open') { ?>
              <p class="tourArticle__ctaLead">国見の暮らしを、実際に歩いて確かめてみませんか？</p>
              <a class="tourArticle__ctaButton" href="<?= e(path(SITE_TOUR['entry_url'])) ?>">参加を申し込む</a>
<?php } else { ?>
              <p class="tourArticle__ctaLead">次回のツアーのご案内はこちらから</p>
              <a class="tourArticle__ctaButton" href="<?= e(path()) ?>#banner">空き家ツアーのご案内</a>
<?php } ?>
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
                    <span class="postIndex__vol"><?= e(tour_number($post)) ?></span>
                    <span class="postIndex__title"><?= e(ContentEngine::format_date($post['date'] ?? '')) ?></span>
                    <span class="postIndex__summary"><?= e(TOUR_STATUSES[tour_status($post)]) ?></span>
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
