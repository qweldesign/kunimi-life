<?php
// ContentEngine start!
require_once __DIR__ . '/inc/ContentEngine.php';
$cms = new ContentEngine();

// APIのアクセス許可
header("Access-Control-Allow-Origin: *");

// JSON出力
// 一覧表示に本文は不要なので除いて返す
$posts = array_map(function ($post) {
  unset($post['content']);
  return $post;
}, $cms->get_posts());

echo json_encode($posts, JSON_UNESCAPED_UNICODE);
return;
