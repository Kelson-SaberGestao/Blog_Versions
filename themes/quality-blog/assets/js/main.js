/* Quality Blog - interacoes do tema.
 *
 * A busca simulada do prototipo saiu daqui: o WordPress tem busca real e o
 * formulario do cabecalho envia direto para ela.
 */
(function () {
  'use strict';

  window.toggleMobileMenu = function () {
    document.getElementById('mobileMenu').classList.toggle('open');
    document.getElementById('mobileMenuBackdrop').classList.toggle('open');
  }

  window.toggleAccordion = function (btn) {
    btn.classList.toggle('open');
    btn.nextElementSibling.classList.toggle('open');
  }

  function copyPostLink(btn) {
    var url = window.location.href;
    function done() {
      btn.classList.add('copied');
      setTimeout(function () { btn.classList.remove('copied'); }, 1800);
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(done).catch(done);
    } else {
      done();
    }
  }
  // Expostas no global porque os templates usam onclick=.
  window.copyPostLink = copyPostLink;
})();
