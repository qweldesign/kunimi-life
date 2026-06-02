<?php
// ContentEngine start!
require_once __DIR__ . '/inc/ContentEngine.php';
$cms = new ContentEngine();
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
    <link href="https://fonts.googleapis.com/css2?family=Klee+One&display=swap" rel="stylesheet">
    <?php if ($cms->is_single()) { ?>
      <link rel="stylesheet" href="../../style.css">
      <link rel="icon" href="../../favicon.ico">
    <?php } else { ?>
      <link rel="stylesheet" href="../style.css">
      <link rel="icon" href="../favicon.ico">
    <?php } ?>
  </head>
  <body>
    <header>
      <div class="hidden" data-site-brand>
        <a href="/">
          <img src="../assets/logo_kunimi-life.png" alt="国見 海・人・暮らし相談室">
        </a>
      </div>
      <h1 class="my-large text-center">この町の人</h1>
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
          <a href="#faq">よくある質問</a>
        </li>
        <li class="gNav__menuItem">
          <a href="/#contact">お申込みフォーム</a>
        </li>
      </ul>
      <ul class="gNav__socialMenu visible-md" data-social-menu>
        <li class="gNav__menuItem is-social" v-if="item">
          <a href="https://instagram.com/kunimi.life" target="_blank">
            <svg class="icon is-si-instagram is-md" width="36" height="36" aria-hidden="true">
              <use href="./assets/icons.svg#si-instagram"></use>
            </svg>
          </a>
        </li>
      </ul>
    </nav>
    <main id="main" class="main">
      <div class="main__container">
        <?php echo $cms->get_breadcrumb(); ?>
        <?php if (!$cms->is_single()) { ?>
          <ul class="postList is-grid">
            <?php foreach ($cms->get_posts() as $post) { ?>
              <li class="postList__item postItem">
                <figure class="postItem__image">
                  <a href="./<?php echo $post['slug']; ?>/">
                    <img loading="lazy" src=".<?php echo $post['img'] ?>">
                  </a>
                </figure>
                <div class="postItem__content">
                  <h3 class="postItem__heading">
                    <a href="./<?php echo $post['slug']; ?>/">
                      <?php echo $post['title']; ?>
                    </a>
                  </h3>
                  <p class="postItem__excerpt">
                    <?php echo $post['summary']; ?>
                  </p>
                  <div class="postItem__info">
                    <?php if (!empty($post['website'])) { ?>
                      <div class="postItem__sns">
                        <a class="postItem__web" href="<?php echo $post['website']; ?>" target="_blank">
                          WEB
                        </a>
                      </div>
                    <?php } ?>
                    <?php if (!empty($post['note'])) { ?>
                      <div class="postItem__sns">
                        <a href="<?php echo $post['note']; ?>" target="_blank">
                          <img src="../assets/icon_note.png" alt="note.com">
                        </a>
                      </div>
                    <?php } ?>
                    <?php if (!empty($post['instagram'])) { ?>
                      <div class="postItem__sns">
                        <a href="<?php echo $post['instagram']; ?>" target="_blank">
                          <img src="../assets/icon_instagram.png" alt="Instagram">
                        </a>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="postItem__more">
                    <a class="icon is-chevron-right" href="./<?php echo $post['slug']; ?>/">
                      <span class="icon__span"></span>
                    </a>
                  </div>
                </div>
              </li>
            <?php } ?>
          </ul>
          <?php echo $cms->pagination(); ?>
          <?php } else { ?>
            <div class="main__row">
              <article class="main__article">
                <?php echo $cms->get_content(); ?>
              </article>
              <aside class="main__aside">
                <ul class="postIndex">
                  <?php foreach ($cms->get_posts(1, 4) as $post) { ?>
                    <li class="postIndex__item">
                      <a href="../<?php echo $post['slug']; ?>/">
                        <img loading="lazy" class="postIndex__image" src="../<?php echo $post['img']; ?>">
                        <span class="postIndex__title"><?php echo $post['title']; ?></span>
                        <span class="postIndex__summary"><?php echo $post['summary']; ?></span>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              </aside>
            </div>
          <?php } ?>
        </div>
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
    <script src="../init.js" type="module"></script>
  </body>
</html>
