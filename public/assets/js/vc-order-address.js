/**
 * Copy buttons on the delivery address block ([data-vc-copy]).
 */
(function () {
    'use strict';

    function legacyCopy(text) {
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.top = '-1000px';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            var ok = false;
            try {
                ok = document.execCommand('copy');
            } catch (e) {
                ok = false;
            }
            ta.remove();
            if (ok) {
                resolve();
            } else {
                reject(new Error('copy failed'));
            }
        });
    }

    function copyText(text) {
        // navigator.clipboard only exists on secure origins (https / localhost).
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text).catch(function () {
                return legacyCopy(text);
            });
        }
        return legacyCopy(text);
    }

    function notify(message, type) {
        if (window.VC && typeof window.VC.toast === 'function') {
            window.VC.toast(message, type);
        } else {
            window.alert(message);
        }
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-vc-copy]');
        if (!btn) {
            return;
        }
        e.preventDefault();
        var text = btn.getAttribute('data-vc-copy') || '';
        copyText(text).then(function () {
            notify(btn.getAttribute('data-vc-copy-done') || 'Copied', 'success');
            btn.classList.add('is-copied');
            setTimeout(function () { btn.classList.remove('is-copied'); }, 1500);
        }, function () {
            window.prompt('Copy this text:', text);
        });
    });
})();
