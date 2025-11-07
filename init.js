/**
 * Auto Copyright
 * このファイルは QWEL Project の一部です。
 * Part of the QWEL Project © QWEL.DESIGN 2025
 * Licensed under GPL v3 – see https://qwel.design/
 */

class AutoCopyright {
  constructor(startYear, companyName, elem) {
    elem ||= document.querySelector('.footer__copyright');
    if (elem) elem.innerHTML = this.generate(startYear, companyName);
  }

  generate(startYear, companyName) {
    const currentYear = new Date().getFullYear();
    if (startYear == currentYear) {
      return `&copy; ${startYear} ${companyName}`
    } else {
      return `&copy; ${startYear} - ${currentYear} ${companyName}`;
    }
  }
}

new AutoCopyright(2025, '国見公民館');


class Embed {
  // 引数infoTextは、カバーに表示させる文字列を受け取る
  // 主にGoogleMapの埋め込みに使用する想定
  constructor(infoText) {
    const elems = document.querySelectorAll('.embed');
    if (!elems || !elems.length) return;

    elems.forEach((elem) => {
      // 要素を生成
      const cover = document.createElement('div');
      cover.classList.add('embed__cover', 'is-active');
      elem.appendChild(cover);
      const info = document.createElement('p');
      info.textContent = infoText || 'クリックするとマップを拡大/縮小できるようになります。';
      cover.appendChild(info);
  
      // 監視
      const myTouch = 'ontouchend' in document && window.innerWidth < 1024 ? 'touchend' : 'click';
      cover.addEventListener(myTouch, () => {
        const promise = this.transitionEnd(cover, () => {
          cover.classList.remove('is-active');
        }).then(() => {
          cover.remove();
        });
      });
    });
  }

  transitionEnd(elem, func) {
    let callback;
    const promise = new Promise((resolve, reject) => {
      callback = () => resolve(elem);
      elem.addEventListener('transitionend', callback);
    });
    func();
    promise.then((elem) => {
      elem.removeEventListener('transitionend', callback);
    });
    return promise;
  }
}

new Embed();
