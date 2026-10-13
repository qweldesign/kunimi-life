<?php
/**
 * 人物の外部リンク (WEB / note / Instagram)
 * 呼び出し側で $post と $base を用意すること
 */
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
        <a href="<?php echo htmlspecialchars($post[$key]); ?>" target="_blank" rel="noopener">
          <img src="<?php echo $base; ?>assets/<?php echo $link['icon']; ?>" alt="" width="20" height="20">
          <span><?php echo $link['label']; ?></span>
        </a>
      </li>
    <?php } ?>
  <?php } ?>
</ul>
