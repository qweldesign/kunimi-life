<?php
/**
 * サイトの設定
 * サイト名や URL, 投稿タイプなど, サイトごとに変わる値はここにまとめる
 * (inc/ 以下のエンジン・テンプレートには, サイト固有の値を書かない)
 */

// 日付の判定・表示のタイムゾーン
date_default_timezone_set('Asia/Tokyo');

// サイト名 (<title> の「記事タイトル | サイト名」と og:site_name に使う)
const SITE_NAME = '国見 海・人・暮らし相談室';
// トップページの <title>
const SITE_HOME_TITLE = '国見 海・人・暮らし相談室 | 福井の海沿いで暮らしを体験する空き家マッチングツアー';
// サイトの URL (末尾のスラッシュなし. OGP, canonical の絶対URLに使う)
const SITE_URL = 'https://kunimi.life';
// サイトのルートのパス (ドメイン直下なら '/'. 例: /tools/simple-cms/ に置く場合)
const SITE_ROOT = '/';
// サイトの説明 (description の既定値)
const SITE_DESCRIPTION = '福井市・越前海岸沿いの町、国見地区。空き家を見学しながら、海辺の暮らしや地域の人とふれあう「空き家マッチングツアー」を開催しています。';
// OGP 画像の既定値 (サイトのルートからのパス. 1200 × 630. 個別に画像を指定したページ・記事はそちらを使う)
const SITE_OGP_IMAGE = 'assets/ogp.jpg';
// OGP 画像の既定値の代替テキスト (トップページに使う)
const SITE_OGP_IMAGE_ALT = '国見 海・人・暮らし相談室 ― 海辺に暮らそう 空き家マッチングツアー';
// 記事一覧のサムネイルの既定値 (記事にアイキャッチがない場合)
const SITE_THUMBNAIL_IMAGE = 'assets/ogp.jpg';
// Google Fonts などの読み込み (不要なら空文字)
const SITE_FONTS_URL = 'https://fonts.googleapis.com/css2?family=Klee+One:wght@400;600&family=Zen+Kaku+Gothic+New:wght@400;700&display=swap';
// 言語
const SITE_LANG = 'ja';
// Google アナリティクスの測定 ID (不要なら空文字)
const SITE_GA_ID = 'G-V1K5PYTB00';

// ヘッダーのロゴ (画像のパスとリンク先)
const SITE_LOGO = 'assets/logo_kunimi-life.png';
const SITE_LOGO_LINK = '';

// ヘッダーのナビ (トップページのセクションの id => 表示名. トップでは #xxx, ほかのページでは /#xxx にする)
// モバイルではドロワーメニューに同じ項目を出す
const SITE_NAV = [
  'banner'  => '空き家ツアー開催概要',
  'intro'   => '当相談室について',
  'flow'    => '空き家マッチングまでの流れ',
  'people'  => 'この町の人',
  'faq'     => 'よくある質問',
  'contact' => '相談室へのお問い合わせ',
];

// パンくずの先頭の階層 (path はサイトのルートからのパス, または http から始まる URL)
const SITE_BREADCRUMB = [
  ['label' => '国見 海・人・暮らし相談室', 'path' => ''],
];

// ナビの SNS など (url は http から始まる URL, またはサイトのルートからのパス. icon は icons.svg の symbol の id)
const SITE_SNS = [
  ['label' => 'Instagram', 'url' => 'https://instagram.com/kunimi.life', 'icon' => 'si-instagram'],
];

// フッターの主催・協力など (見出し => 内容)
const SITE_CREDITS = [
  '主催'     => '国見 海・人・暮らし相談室 (国見移住促進委員会)',
  '協力'     => '国見地区自治会連合会、国見公民館、越前海岸盛り上げ隊',
  '特別協力' => 'ふるさと福井サポートセンター',
  '後援'     => '福井市',
];

/**
 * 投稿タイプ
 * キー: 投稿タイプの名前
 * label: 表示名 (一覧の見出し, パンくず)
 * path: 一覧の URL (サイトのルートからのパス. 同じ名前のディレクトリに index.php と .htaccess を置く)
 * dir: Markdown を置くディレクトリ (サイトのルートからのパス)
 * list: 一覧ページをサイトマップに載せるか (規定で true)
 * count: 一覧の1ページあたりの件数 (null で全件)
 * show_date: 日付を表示するか
 * lead: 一覧の見出しの下の文
 * description: 一覧の description (規定で lead)
 */
const SITE_POST_TYPES = [
  // ツアー (1回のツアー = 1記事. frontmatter に開催要項, 本文に開催後の報告を書く. slug が回数 01, 02, … を兼ねる)
  // frontmatter: slug, title, date, time, deadline, meeting, meeting_note, fee, fee_note,
  //              summary, img, participants, inquiries, status
  'tour' => [
    'label'       => '空き家マッチングツアー',
    'path'        => 'tour/',
    'dir'         => 'content/tour',
    'count'       => 12,
    'show_date'   => true,
    'lead'        => 'これまでに開催した空き家マッチングツアーの様子と記録をご報告します。',
    'description' => '福井市国見地区で開催している「空き家マッチングツアー」の開催報告。当日の様子や参加者数、ツアー後のお問い合わせ件数をまとめています。',
  ],
  // この町の人 (インタビュー. slug が連番 vol.01, 02, … を兼ねる. 並びは slug 順)
  // frontmatter: slug, catch, name, summary, img, website, note, instagram
  'people' => [
    'label'       => 'この町の人',
    'path'        => 'people/',
    'dir'         => 'content/people',
    'count'       => 10,
    'show_date'   => false,
    'lead'        => '国見には、いろいろな人が暮らしています。ここでは、そんな「この町の人」に話を聞きながら、国見での暮らしや、この町の魅力をご紹介します。',
    'description' => '国見地区で暮らす人、移住してきた人、地域に関わる人。「この町の人」に話を聞きながら、国見での暮らしや町の魅力を紹介します。',
  ],
];

/**
 * ツアーの共通項目 (トップページの開催概要 #banner と /tour/ で使う)
 * area: ツアー名の上に出す地域名
 * entry_url: お申込みフォームの URL (サイトのルートからのパス. フォームは別のリポジトリ kunimi-life-form)
 * line_url: 次回のお知らせを受け取る LINE 友だち追加の URL (開催済みで次回が未定のときに出す)
 * contact: お問い合わせ先 (label: 表示, tel: 発信用の番号, note: 補足)
 */
const SITE_TOUR = [
  'area'      => '福井県福井市 国見地区',
  'entry_url' => 'entry/',
  'line_url'  => 'https://lin.ee/YsVWzb6W',
  'contact'   => ['label' => '0776-88-2004', 'tel' => '0776882004', 'note' => '国見公民館'],
];

// URL の形式 (false: ?slug=xxx, ?page=2 / true: xxx/, page/2/ ※ .htaccess のリライトが必要)
const SITE_PRETTY_URLS = true;

// 投稿タイプ以外のページ (サイトのルートからのパス. サイトマップに載せる)
const SITE_PAGES = [];

/**
 * お問い合わせフォーム (入力: トップページの #contact-form, 確認・完了: /contact/confirm/, /contact/thanks/)
 * SMTP のパスワードなど秘密の値は inc/.env に書く (inc/.env.example を参照)
 * ※ ツアーのお申込みフォーム (/entry/) は別のリポジトリ (kunimi-life-form)
 *
 * path: フォームの URL (サイトのルートからのパス. 同じ名前のディレクトリに index.php, confirm/, thanks/ を置く. 送信は api/send.php)
 * form_url: 入力画面を別の場所 (トップページのセクションなど) に置く場合の URL (サイトのルートからのパス)
 * title: ページのタイトル
 * form_title: 入力画面の見出し
 * interval: 同じ人 (セッション) が続けて送信できるまでの秒数 (0 で制限しない)
 * reply_footer: 自動返信メールの末尾に付ける文
 * env: .env の場所 (公開ディレクトリの外に置く場合は, このパスを書き換える)
 */
const SITE_CONTACT = [
  'path'         => 'contact/',
  'form_url'     => '#contact-form',
  'title'        => 'お問い合わせ',
  'form_title'   => 'メールフォーム',
  'interval'     => 60,
  'reply_footer' => "
後日ご返信致しますので今しばらくお待ちください。

────────────────────────────────────────────────
国見 海・人・暮らし相談室
お問い合わせ: 0776-88-2004 (国見公民館)
────────────────────────────────────────────────
",
  'env'          => __DIR__ . '/inc/.env',
];

/**
 * お問い合わせフォームの項目 (キーが name 属性. 並び順に表示する)
 * label: 項目名 (確認画面とメール本文にも使う)
 * type: text, email, tel, url, textarea, select, radio, checkbox
 *       (email の最初の項目を, 自動返信の宛先と管理者宛メールの返信先にする)
 * required: 必須か
 * options: select, radio, checkbox の選択肢
 * placeholder, autocomplete, rows: 入力欄の属性 (任意)
 */
const SITE_CONTACT_FIELDS = [
  'name' => [
    'label'        => '代表者様氏名',
    'type'         => 'text',
    'required'     => true,
    'autocomplete' => 'name',
  ],
  'email' => [
    'label'        => 'メールアドレス',
    'type'         => 'email',
    'required'     => true,
    'autocomplete' => 'email',
  ],
  'tel' => [
    'label'        => '電話番号',
    'type'         => 'tel',
    'autocomplete' => 'tel',
  ],
  'subject' => [
    'label'   => '件名',
    'type'    => 'select',
    'options' => ['ご質問・ご意見', 'その他'],
  ],
  'message' => [
    'label' => '備考',
    'type'  => 'textarea',
    'rows'  => 6,
  ],
];
