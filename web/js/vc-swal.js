/**
 * VeggiiCart SweetAlert2 helpers — admin + website.
 */
(function (global) {
    'use strict';

    if (typeof Swal === 'undefined') {
        return;
    }

    var VC_PRIMARY = '#12833B';
    var VC_DANGER = '#D64545';

    var baseOptions = {
        heightAuto: false,
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-primary mx-1',
            cancelButton: 'btn btn-outline-secondary mx-1',
            denyButton: 'btn btn-outline-danger mx-1'
        }
    };

    function mergeOptions(opts) {
        var options = Object.assign({}, baseOptions, opts || {});
        if (opts && opts.customClass) {
            options.customClass = Object.assign({}, baseOptions.customClass, opts.customClass);
        }
        return options;
    }

    function vcAlert(message, opts) {
        opts = opts || {};
        return Swal.fire(mergeOptions({
            icon: opts.icon || 'info',
            title: opts.title || '',
            text: String(message == null ? '' : message),
            confirmButtonText: opts.confirmText || 'OK',
            confirmButtonColor: opts.confirmColor || VC_PRIMARY
        }));
    }

    function vcConfirm(message, opts) {
        opts = opts || {};
        var danger = !!opts.danger;
        return Swal.fire(mergeOptions({
            icon: opts.icon || 'warning',
            title: opts.title || 'Are you sure?',
            text: String(message == null ? '' : message),
            showCancelButton: true,
            confirmButtonText: opts.confirmText || (danger ? 'Yes, delete' : 'Yes, continue'),
            cancelButtonText: opts.cancelText || 'Cancel',
            reverseButtons: true,
            focusCancel: danger,
            confirmButtonColor: danger ? VC_DANGER : VC_PRIMARY,
            customClass: {
                confirmButton: danger ? 'btn btn-danger mx-1' : 'btn btn-primary mx-1',
                cancelButton: 'btn btn-outline-secondary mx-1'
            }
        })).then(function (result) {
            return !!result.isConfirmed;
        });
    }

    function vcPrompt(message, opts) {
        opts = opts || {};
        var html = opts.html || '';
        if (opts.devOtp) {
            html = (html ? html + '<br>' : '') +
                '<p class="mb-2 text-muted small">DEV OTP: <strong>' + String(opts.devOtp) + '</strong></p>';
        }
        return Swal.fire(mergeOptions({
            icon: opts.icon || 'question',
            title: opts.title || 'Input required',
            text: html ? undefined : String(message == null ? '' : message),
            html: html || undefined,
            input: opts.input || 'text',
            inputLabel: opts.inputLabel || '',
            inputPlaceholder: opts.placeholder || '',
            inputValue: opts.value || '',
            showCancelButton: true,
            confirmButtonText: opts.confirmText || 'Submit',
            cancelButtonText: opts.cancelText || 'Cancel',
            confirmButtonColor: VC_PRIMARY,
            inputValidator: opts.validator || function (value) {
                if (value == null || String(value).trim() === '') {
                    return 'This field is required';
                }
            }
        })).then(function (result) {
            return result.isConfirmed ? result.value : null;
        });
    }

    function vcToast(message, type) {
        var icon = 'info';
        if (type === 'error') {
            icon = 'error';
        } else if (type === 'success') {
            icon = 'success';
        } else if (type === 'warning') {
            icon = 'warning';
        }
        return Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: String(message == null ? '' : message),
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
            heightAuto: false
        });
    }

    function bindConfirmForms() {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.getAttribute) {
                return;
            }
            var msg = form.getAttribute('data-vc-confirm');
            if (!msg) {
                return;
            }
            if (form.dataset.vcConfirmed === '1') {
                delete form.dataset.vcConfirmed;
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            vcConfirm(msg, {
                title: form.getAttribute('data-vc-confirm-title') || 'Confirm',
                confirmText: form.getAttribute('data-vc-confirm-yes') || undefined,
                danger: form.hasAttribute('data-vc-confirm-danger')
            }).then(function (ok) {
                if (ok) {
                    form.dataset.vcConfirmed = '1';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }
            });
        }, true);
    }

    function bindConfirmButtons() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-vc-confirm]');
            if (!btn || btn.tagName === 'FORM') {
                return;
            }
            if (btn.type !== 'submit') {
                return;
            }
            var form = btn.form;
            if (!form) {
                var formId = btn.getAttribute('form');
                form = formId ? document.getElementById(formId) : null;
            }
            if (!form || form.getAttribute('data-vc-confirm')) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            var msg = btn.getAttribute('data-vc-confirm');
            vcConfirm(msg, {
                title: btn.getAttribute('data-vc-confirm-title') || 'Confirm',
                confirmText: btn.getAttribute('data-vc-confirm-yes') || undefined,
                danger: btn.hasAttribute('data-vc-confirm-danger')
            }).then(function (ok) {
                if (ok) {
                    form.dataset.vcConfirmed = '1';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit(btn);
                    } else {
                        form.submit();
                    }
                }
            });
        }, true);
    }

    function showFlashAlerts() {
        document.querySelectorAll('.alert.alert-success, .alert.alert-danger, .alert.alert-warning, .alert.alert-info').forEach(function (el) {
            if (el.closest('form') && !el.classList.contains('vc-login-alert')) {
                return;
            }
            var text = (el.textContent || '').replace(/\s+/g, ' ').trim();
            if (!text) {
                return;
            }
            var type = 'info';
            if (el.classList.contains('alert-success')) {
                type = 'success';
            } else if (el.classList.contains('alert-danger')) {
                type = 'error';
            } else if (el.classList.contains('alert-warning')) {
                type = 'warning';
            }
            el.style.display = 'none';
            vcToast(text, type);
        });
    }

    global.alert = function (message) {
        vcAlert(String(message == null ? '' : message), { icon: 'info', title: '' });
    };

    global.VC = global.VC || {};
    global.VC.alert = vcAlert;
    global.VC.confirm = vcConfirm;
    global.VC.prompt = vcPrompt;
    global.VC.toast = vcToast;

    function init() {
        bindConfirmForms();
        bindConfirmButtons();
        showFlashAlerts();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(typeof window !== 'undefined' ? window : this);
