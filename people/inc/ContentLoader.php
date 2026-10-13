<?php
/**
 * ContentLoader.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

class ContentLoader {
	private string $dir;

	public function __construct(string $dir) {
		$this->dir = $dir;
	}

	public function load() {
		$articles = [];
		foreach (glob("$this->dir/*.md") as $file) {
			$parsed = $this->parse($file);
			if ($parsed) $articles[] = $parsed;
		}
		// 日付ASC (date が無い記事は slug 順)
		usort($articles, fn($a, $b) => [$a['date'] ?? '', $a['slug'] ?? ''] <=> [$b['date'] ?? '', $b['slug'] ?? '']);
		return $articles;
	}

	public function find($slug) {
		foreach (glob("$this->dir/*.md") as $file) {
			$parsed = $this->parse($file);
			if ($parsed && $parsed['slug'] === $slug) return $parsed;
		}
		return null;
	}

	protected function parse($file) {
		$content = file_get_contents($file);
		if (!$content) return null;

		if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $content, $matches)) {
			$front = $matches[1];
			$body  = $matches[2];

			$meta = [];
			foreach (explode("\n", trim($front)) as $line) {
				if (preg_match('/^(\w+):\s*"?(.+?)"?$/', trim($line), $m)) {
					$meta[$m[1]] = $m[2];
				}
			}

			$meta['content'] = $body;
			return $this->split_title($meta);
		}

		return null;
	}

	// タイトル「vol.N キャッチ ― 氏名」を vol / catch / name に分解する
	// 形式に合わない場合は title をそのまま catch として使う
	protected function split_title(array $meta): array {
		$title = $meta['title'] ?? '';
		if (preg_match('/^vol\.(\d+)\s*(.+?)\s*―\s*(.+)$/u', $title, $t)) {
			$meta['vol']   = $t[1];
			$meta['catch'] = $t[2];
			$meta['name']  = $t[3];
		} else {
			$meta['vol']   = '';
			$meta['catch'] = $title;
			$meta['name']  = '';
		}
		return $meta;
	}
}
