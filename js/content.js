/**
 * Content
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

// トップページ「この町の人」: people/api.php の記事をカードで並べる
export default class Content {
  constructor() {
    this.template = document.getElementById('personTemplate');
    this.list = document.querySelector('[data-people-list]');
    // テンプレートが無いページ (/people/ 配下など) では何もしない
    if (!this.template || !this.list) return;
    this.init();
  }

  async init() {
    try {
      const res = await fetch('./people/api.php');
      const data = await res.json();
      this.render(data);
    } catch (error) {
      console.error('Error fetching people data:', error);
    }
  }

  render(data) {
    data.forEach(person => {
      const clone = this.template.content.cloneNode(true);
      const link = clone.querySelector('.personCard__link');
      const image = clone.querySelector('.personCard__image img');

      link.href = `people/${person.slug}/`;
      image.src = person.img; // ルートからの絶対パス (/content/images/people/...)
      image.alt = person.name || '';

      clone.querySelector('.personCard__vol').textContent = `vol.${person.slug}`; // slug が連番 (01, 02, …) を兼ねる
      clone.querySelector('.personCard__catch').textContent = person.catch || '';
      clone.querySelector('.personCard__name').textContent = person.name || '';
      clone.querySelector('.personCard__role').textContent = person.summary || '';

      this.list.appendChild(clone);
    });
  }
}
