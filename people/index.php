<?php
/**
 * この町の人 (一覧: /people/, 個別記事: /people/xx/)
 * 記事は content/people/*.md に置く. URL は people/.htaccess のリライトで振り分ける
 */
require_once dirname(__DIR__) . '/inc/templates/people.php';

render_people();
