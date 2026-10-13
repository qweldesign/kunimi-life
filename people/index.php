<?php
// ContentEngine start!
require_once __DIR__ . '/inc/ContentEngine.php';
$cms = new ContentEngine();

// ルートへの相対パス (/people/ と /people/xx/ で階層が異なる)
$base = $cms->is_single() ? '../../' : '../';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($cms->is_single()) { ?>
      <title><?php echo $cms->get_title() . ' | '; ?>この町の人 | 国見 海・人・暮らし相談室</title>
    <?php } else { ?>
      <title>この町の人 | 国見 海・人・暮らし相談室</title>
    <?php } ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Klee+One:wght@400;600&family=Zen+Kaku+Gothic+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>style-1.3.css">
    <link rel="icon" href="<?php echo $base; ?>favicon.ico">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BK66SEG6VG"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-BK66SEG6VG');
    </script>
  </head>
  <body>
    <header class="pageHeader">
      <div class="hidden" data-site-brand>
        <a href="/">
          <img src="<?php echo $base; ?>assets/logo_kunimi-life.png" alt="国見 海・人・暮らし相談室">
        </a>
      </div>
      <p class="pageHeader__eyebrow"><a href="/">国見 海・人・暮らし相談室</a></p>
      <?php if ($cms->is_single()) { ?>
        <p class="pageHeader__title"><a href="/people/">この町の人</a></p>
      <?php } else { ?>
        <h1 class="pageHeader__title">この町の人</h1>
        <p class="pageHeader__lead">国見には、いろいろな人が暮らしています。ここでは、そんな「この町の人」に話を聞きながら、国見での暮らしや、この町の魅力をご紹介します。</p>
      <?php } ?>
    </header>
    <nav class="gNav hidden">
      <ul class="gNav__primaryMenu visible-md" data-primary-menu>
        <li class="gNav__menuItem">
          <a href="/#banner">空き家ツアー開催概要</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#intro">当相談室について</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#flow">空き家マッチングまでの流れ</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#people">この町の人</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#faq">よくある質問</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#contact">お申込みフォーム</a>
        </li>
      </ul>
      <ul class="gNav__socialMenu visible-md" data-social-menu>
        <li class="gNav__menuItem is-social" v-if="item">
          <a href="https://instagram.com/kunimi.life" target="_blank">
            <svg class="icon is-si-instagram is-md" width="36" height="36" aria-hidden="true">
              <use href="<?php echo $base; ?>assets/icons.svg#si-instagram"></use>
            </svg>
          </a>
        </li>
      </ul>
    </nav>
    <main id="main" class="main">
      <div class="main__container">
        <?php echo $cms->get_breadcrumb(); ?>
        <?php if (!$cms->is_single()) { ?>
          <ul class="personGrid">
            <?php foreach ($cms->get_posts() as $post) { ?>
              <li class="personCard">
                <a class="personCard__link" href="./<?php echo $post['slug']; ?>/">
                  <figure class="personCard__image">
                    <img loading="lazy" src=".<?php echo $post['img']; ?>" alt="<?php echo htmlspecialchars($post['name'] ?: $post['title']); ?>">
                  </figure>
                  <div class="personCard__body">
                    <?php if ($post['vol'] !== '') { ?>
                      <p class="personCard__vol">vol.<?php echo $post['vol']; ?></p>
                    <?php } ?>
                    <h2 class="personCard__catch"><?php echo $post['catch']; ?></h2>
                    <p class="personCard__name"><?php echo $post['name']; ?></p>
                    <p class="personCard__role"><?php echo $post['summary']; ?></p>
                  </div>
                </a>
                <?php include __DIR__ . '/inc/person-links.php'; ?>
              </li>
            <?php } ?>
          </ul>
          <?php echo $cms->pagination(); ?>
        <?php } else { ?>
          <?php $article = $cms->get_article(); ?>
          <div class="main__row">
            <article class="main__article peopleArticle">
              <?php echo $cms->get_content(); ?>
              <?php $post = $article; ?>
              <?php if (!empty($post['website']) || !empty($post['note']) || !empty($post['instagram'])) { ?>
                <section class="peopleArticle__links">
                  <h3 class="peopleArticle__linksTitle"><?php echo $article['name'] ?: 'この方'; ?>の活動をもっと見る</h3>
                  <?php include __DIR__ . '/inc/person-links.php'; ?>
                </section>
              <?php } ?>
              <?php $adjacent = $cms->get_adjacent(); ?>
              <nav class="peopleArticle__pager" aria-label="前後の記事">
                <?php foreach (['prev' => '前の記事', 'next' => '次の記事'] as $key => $label) { ?>
                  <?php if ($adjacent[$key]) { ?>
                    <a class="peopleArticle__pagerItem is-<?php echo $key; ?>" href="../<?php echo $adjacent[$key]['slug']; ?>/">
                      <span class="peopleArticle__pagerLabel"><?php echo $label; ?></span>
                      <span class="peopleArticle__pagerName">vol.<?php echo $adjacent[$key]['vol']; ?> <?php echo $adjacent[$key]['name']; ?></span>
                    </a>
                  <?php } else { ?>
                    <span class="peopleArticle__pagerItem is-<?php echo $key; ?> is-empty"></span>
                  <?php } ?>
                <?php } ?>
              </nav>
              <aside class="peopleArticle__cta">
                <p class="peopleArticle__ctaLead">国見の暮らしを、実際に歩いて確かめてみませんか？</p>
                <a class="peopleArticle__ctaButton" href="/#banner">空き家ツアーのご案内</a>
              </aside>
            </article>
            <aside class="main__aside">
              <h2 class="postIndex__heading">この町の人</h2>
              <ul class="postIndex">
                <?php foreach ($cms->get_posts(1, 10) as $post) { ?>
                  <li class="postIndex__item<?php echo $post['slug'] === $article['slug'] ? ' is-current' : ''; ?>">
                    <a href="../<?php echo $post['slug']; ?>/"<?php echo $post['slug'] === $article['slug'] ? ' aria-current="page"' : ''; ?>>
                      <img loading="lazy" class="postIndex__image" src="..<?php echo $post['img']; ?>" alt="">
                      <span class="postIndex__text">
                        <span class="postIndex__vol">vol.<?php echo $post['vol']; ?></span>
                        <span class="postIndex__title"><?php echo $post['name'] ?: $post['title']; ?></span>
                        <span class="postIndex__summary"><?php echo $post['summary']; ?></span>
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
    <footer id="footer" class="footer">
      <div class="footer__inner">
        <table class="footer__table">
          <tr>
            <td>主催:</td>
            <td>国見 海・人・暮らし相談室 (国見移住促進委員会)</td>
          </tr>
          <tr>
            <td>協力:</td>
            <td>国見地区自治会連合会、国見公民館、越前海岸盛り上げ隊</td>
          </tr>
          <tr>
            <td>特別協力:</td>
            <td>ふるさと福井サポートセンター</td>
          </tr>
          <tr>
            <td>後援:</td>
            <td>福井市</td>
          </tr>
        </table>
      </div>
    </footer>
    <script src="<?php echo $base; ?>init.js" type="module"></script>
  </body>
</html>
