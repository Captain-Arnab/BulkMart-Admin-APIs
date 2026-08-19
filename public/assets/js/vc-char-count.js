(function () {
  function bindCharCounts(root) {
    (root || document).querySelectorAll('[data-char-count][maxlength]').forEach(function (el) {
      if (el.dataset.charBound) {
        return;
      }
      el.dataset.charBound = '1';
      var max = parseInt(el.getAttribute('maxlength'), 10) || 0;
      var hint = el.parentNode.querySelector('.vc-char-hint');
      if (!hint) {
        hint = document.createElement('div');
        hint.className = 'form-text vc-char-hint';
        el.insertAdjacentElement('afterend', hint);
      }
      function tick() {
        hint.textContent = (el.value || '').length + ' / ' + max + ' characters';
      }
      el.addEventListener('input', tick);
      tick();
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { bindCharCounts(document); });
  } else {
    bindCharCounts(document);
  }
})();
