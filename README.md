# 国見 海・人・暮らし相談室

国見 海・人・暮らし相談室 ― 海辺に暮らそう 空き家マッチングツアー  

[https://kunimi.life](https://kunimi.life)  

※ このリポジトリは、[QWEL Markdown Engine (Simple CMS)](https://github.com/qweldesign/qwel-tools-md-engine) を使用して制作しています。  

---

## 投稿 (この町の人)

[qwel-tools-md-engine](https://github.com/qweldesign/qwel-tools-md-engine) をベースにした Markdown のコンテンツエンジン (`inc/`)。PHP が動くサーバーで, Markdown を置くだけでページが増える。

| 投稿タイプ | 原稿の置き場所 | URL |
| --- | --- | --- |
| people (この町の人) | `content/people/*.md` | 一覧 `/people/`, `/people/page/2/` / 個別 `/people/xxx/` |

サイト名・ナビ・投稿タイプ (表示名や説明文)・お問い合わせフォームの項目などは `config.php` にまとめている。投稿タイプを増やす場合は, `config.php` の `SITE_POST_TYPES` に追加し, 同じ形の `index.php` と `.htaccess` を持つディレクトリを作り, `content/` に同名のディレクトリを用意する。サイトマップ (`/sitemap.xml`) は `sitemap.php` が `content/` から自動で作る。

URL は各ディレクトリの `.htaccess` のリライト (Apache の mod_rewrite) で `index.php?slug=xxx` に振り分けている。旧形式の `?slug=xxx` で来た場合は新しい URL へ 301 で転送する。サイトをドメイン直下以外に置く場合は, `config.php` の `SITE_ROOT` を書き換える。info と report の slug に `page` は使えない (ページ送りの URL と重なるため)。

原稿の冒頭に frontmatter を書く。

```
---
slug: "vol.01"
catch: "キャッチコピー"
name: "氏名"
summary: "一覧や description に使う要約"
img: "/content/images/..."
website: "https://..."
note: "https://note.com/..."
instagram: "https://www.instagram.com/..."
---
```

## お問い合わせフォーム

トップページのフォーム (`#contact`) → 確認画面 `/contact/confirm/` → 送信 `api/send.php` (PHPMailer) → 完了画面 `/contact/thanks/`。入力内容は SessionStorage で確認画面へ受け渡す (`js/contact-form.js`)。

- `inc/` で `composer install` を実行して PHPMailer を入れる
- `inc/.env.example` を `inc/.env` にコピーし, SMTP などの設定を書く (`.env` は Git に含めない)
- 項目・自動返信の署名などは `config.php` の `SITE_CONTACT`, `SITE_CONTACT_FIELDS` で設定する (入力欄・確認画面・メール本文・サーバー側の検証をまとめて作る)
- 迷惑メール対策: Origin / Referer のチェック, 見えない入力欄 (ハニーポット), 同じセッションからの送信間隔の制限

---

## ライセンス | License

### コード部分 | Code

MIT License

詳しくは LICENSE ファイルをご覧ください。  
See the LICENSE file for details.

### 写真・文章・その他コンテンツ | Contents

写真・文章・その他コンテンツの著作権は全て、国見 海・人・暮らし相談室に帰属します。  
複製・再利用・再配布は、明示的な許可なしには認められていません。

All copyrights in the photos, texts, and other content belong to KUNIMI.LIFE.  
Reproduction, reuse, or redistribution without explicit permission is prohibited.

---

## 制作者 | Author

[QWEL.DESIGN](https://qwel.design)  
福井を拠点に活動するフロントエンド開発者  
Front-end developer based in Fukui, Japan  
