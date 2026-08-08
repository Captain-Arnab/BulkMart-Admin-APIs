/**
 * Header module search — filter sidebar modules and jump on select.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('vc-module-search');
    var input = document.getElementById('vc-module-search-input');
    var results = document.getElementById('vc-module-search-results');
    var clearBtn = document.getElementById('vc-module-search-clear');
    var modules = window.VC_MODULES || [];
    if (!root || !input || !results) return;

    var activeIndex = -1;

    function closeResults() {
      results.hidden = true;
      results.innerHTML = '';
      activeIndex = -1;
      input.setAttribute('aria-expanded', 'false');
    }

    function openResults(html) {
      results.innerHTML = html;
      results.hidden = !html;
      input.setAttribute('aria-expanded', html ? 'true' : 'false');
      activeIndex = html ? 0 : -1;
      highlightActive();
    }

    function highlightActive() {
      var items = results.querySelectorAll('.vc-search-item');
      items.forEach(function (el, i) {
        el.classList.toggle('is-active', i === activeIndex);
      });
    }

    function render(query) {
      var q = (query || '').trim().toLowerCase();
      clearBtn.hidden = q === '';
      if (q.length < 1) {
        closeResults();
        return;
      }

      var matches = modules.filter(function (m) {
        return (m.label || '').toLowerCase().indexOf(q) !== -1
          || (m.route || '').toLowerCase().indexOf(q) !== -1;
      }).slice(0, 8);

      if (!matches.length) {
        openResults('<div class="vc-search-empty">No modules match “' + escapeHtml(query) + '”</div>');
        activeIndex = -1;
        return;
      }

      var html = matches.map(function (m, i) {
        return '<a class="vc-search-item' + (i === 0 ? ' is-active' : '') + '" role="option" href="' + escapeAttr(m.url) + '">'
          + '<span class="vc-search-item-icon"><i class="bi ' + escapeAttr(m.icon || 'bi-grid') + '"></i></span>'
          + '<span class="vc-search-item-text">'
          + '<span class="vc-search-item-label">' + escapeHtml(m.label) + '</span>'
          + '<span class="vc-search-item-route">/' + escapeHtml(m.route) + '</span>'
          + '</span></a>';
      }).join('');
      openResults(html);
    }

    function escapeHtml(s) {
      return String(s).replace(/[&<>"']/g, function (c) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
      });
    }
    function escapeAttr(s) {
      return escapeHtml(s).replace(/`/g, '');
    }

    input.addEventListener('input', function () {
      render(input.value);
    });

    input.addEventListener('keydown', function (e) {
      var items = results.querySelectorAll('.vc-search-item');
      if (e.key === 'Escape') {
        closeResults();
        input.blur();
        return;
      }
      if (e.key === 'ArrowDown' && items.length) {
        e.preventDefault();
        activeIndex = Math.min(items.length - 1, activeIndex + 1);
        highlightActive();
      } else if (e.key === 'ArrowUp' && items.length) {
        e.preventDefault();
        activeIndex = Math.max(0, activeIndex - 1);
        highlightActive();
      } else if (e.key === 'Enter' && items.length && activeIndex >= 0) {
        e.preventDefault();
        window.location.href = items[activeIndex].getAttribute('href');
      }
    });

    clearBtn.addEventListener('click', function () {
      input.value = '';
      clearBtn.hidden = true;
      closeResults();
      input.focus();
    });

    document.addEventListener('click', function (e) {
      if (!root.contains(e.target)) closeResults();
    });

    document.addEventListener('keydown', function (e) {
      var meta = e.metaKey || e.ctrlKey;
      if (meta && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        input.focus();
        input.select();
      }
    });
  });
})();
