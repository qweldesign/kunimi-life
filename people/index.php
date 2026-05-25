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
      <h1 class="text-center my-large">この町の人</h1>
    </header>
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
                  <div class="postItem__info">
                    <div class=postItem__date><?php echo date('Y.m.d',strtotime($post['date'])); ?></div>
                  </div>
                  <h3 class="postItem__heading">
                    <a href="./<?php echo $post['slug']; ?>/">
                      <?php echo $post['title']; ?>
                    </a>
                  </h3>
                  <p class="postItem__excerpt">
                    <?php echo $post['summary']; ?>
                    <a class="postItem__more" href="./<?php echo $post['slug']; ?>/">view more &gt;</a>
                  </p>
                </div>
              </li>
            <?php } ?>
          </ul>
          <?php echo $cms->pagination(); ?>
          <?php } else { ?>
            <div class="main__row">
              <article class="main__article">
                <div><?php echo $cms->get_date(); ?></div>
                <?php echo $cms->get_content(); ?>
              </article>
              <aside class="main__aside">
                <h2>最新記事</h2>
                <ul class="postIndex">
                  <?php foreach ($cms->get_posts(1, 4) as $post) { ?>
                    <li class="postIndex__item">
                      <a href="../<?php echo $post['slug']; ?>/">
                        <img loading="lazy" class="postIndex__image" src="../<?php echo $post['img']; ?>">
                        <span class="postIndex__date"><?php echo date('Y.m.d',strtotime($post['date'])); ?></span>
                        <span class="postIndex__title"><?php echo $post['title']; ?></span>
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
