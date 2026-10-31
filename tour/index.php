<?php
/**
 * 空き家マッチングツアー (一覧: /tour/, 個別記事 (開催報告): /tour/xx/)
 * 記事は content/tour/*.md に置く. URL は tour/.htaccess のリライトで振り分ける
 */
require_once dirname(__DIR__) . '/inc/templates/tour.php';

render_tours();
