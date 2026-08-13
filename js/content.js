/**
 * Content
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

import Slider from './slider.js';

export default class Content {
  constructor() {
    this.template = document.getElementById('personTemplate');
    this.inner = document.querySelector('.slider__inner');
    this.init();
  }

  async init() {
    try {
      const res = await fetch('./people/api.php');
      const data = await res.json();
      this.render(data);

      // Slider
      new Slider();

    } catch (error) {
      console.error('Error fetching people data:', error);
    }
  }

  render(data) {
    data.forEach(person => {
      const clone = this.template.content.cloneNode(true);
      const personElement = clone.querySelector('.slider__item');
      personElement.querySelector('.slider__image').src = `people/${person.img}`;
      personElement.querySelector('.slider__link').innerHTML = `${person.title}<br><span>${person.summary}</span>`;
      personElement.querySelector('.slider__link').href = `people/${person.slug}/`;
      this.inner.appendChild(clone);
    });
  }
}
