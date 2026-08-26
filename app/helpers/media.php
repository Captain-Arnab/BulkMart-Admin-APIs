<?php

function vc_strlen(string $s): int
{
    return function_exists('mb_strlen') ? (int) mb_strlen($s, 'UTF-8') : strlen($s);
}

function media_accept_attr(): string
{
    return '.jpg,.jpeg,.png,.gif,.webp,.avif,.bmp,.pdf,image/jpeg,image/png,image/gif,image/webp,image/avif,image/bmp,application/pdf';
}

function media_formats_hint(): string
{
    return 'JPG, JPEG, PNG, GIF, WEBP, AVIF, BMP, or PDF (browser-previewable). Max 5MB.';
}

function media_is_pdf(?string $path): bool
{
    if ($path === null || $path === '') {
        return false;
    }
    $path = (string) (parse_url($path, PHP_URL_PATH) ?: $path);
    return (bool) preg_match('/\.pdf$/i', $path);
}

function media_thumb_html(?string $path, string $imgClass = '', string $imgStyle = ''): string
{
    if ($path === null || $path === '') {
        return '';
    }
    $url = e(media($path));
    $classAttr = $imgClass !== '' ? ' class="' . e($imgClass) . '"' : '';
    $styleAttr = $imgStyle !== '' ? ' style="' . e($imgStyle) . '"' : '';
    if (media_is_pdf($path)) {
        return '<a href="' . $url . '" target="_blank" rel="noopener" class="vc-pdf-thumb' . ($imgClass !== '' ? ' ' . e($imgClass) : '') . '"' . $styleAttr . ' title="Open PDF"><i class="bi bi-file-earmark-pdf-fill"></i></a>';
    }
    return '<img src="' . $url . '" alt=""' . $classAttr . $styleAttr . '>';
}

function media_preview_html(?string $path): string
{
    if ($path === null || $path === '') {
        return '';
    }
    $url = e(media($path));
    if (media_is_pdf($path)) {
        return '<iframe class="vc-media-preview-pdf" src="' . $url . '" title="PDF preview"></iframe>';
    }
    return '<img src="' . $url . '" alt="">';
}

function app_settings_keyed(bool $forceRefresh = false): array
{
    static $cache = null;
    if ($forceRefresh) {
        $cache = null;
    }
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    try {
        if (class_exists('AppSetting')) {
            $cache = (new AppSetting())->allKeyed();
        }
    } catch (Throwable $e) {
        $cache = [];
    }
    return $cache;
}

function app_settings_reset_cache(): void
{
    app_settings_keyed(true);
}

function admin_brand_src(string $key, string $fallbackAsset): string
{
    $v = trim((string) (app_settings_keyed()[$key] ?? ''));
    if ($v !== '') {
        if (preg_match('#^https?://#i', $v)) {
            $url = $v;
        } else {
            $url = public_url(ltrim($v, '/'));
        }
    } else {
        $url = public_url('assets/' . ltrim($fallbackAsset, '/'));
    }
    $rev = trim((string) (app_settings_keyed()['admin_brand_rev'] ?? ''));
    if ($v !== '' && $rev !== '') {
        $url .= (str_contains($url, '?') ? '&' : '?') . 'v=' . rawurlencode($rev);
    }
    return $url;
}

function site_logo_src(): string
{
    $v = trim((string) (app_settings_keyed()['admin_logo_url'] ?? ''));
    if ($v !== '') {
        return admin_brand_src('admin_logo_url', 'img/logo-on-light.png');
    }
    return 'images/vegiicart-logo.jpeg';
}

function site_favicon_src(): string
{
    $v = trim((string) (app_settings_keyed()['admin_favicon_url'] ?? ''));
    if ($v !== '') {
        return admin_brand_src('admin_favicon_url', 'img/logo-mark.png');
    }
    $logo = trim((string) (app_settings_keyed()['admin_logo_url'] ?? ''));
    if ($logo !== '') {
        return admin_brand_src('admin_logo_url', 'img/logo-mark.png');
    }
    return 'images/vegiicart-logo.jpeg';
}

function admin_logo_src(): string
{
    return admin_brand_src('admin_logo_url', 'img/logo-on-light.png');
}

function admin_favicon_src(): string
{
    return admin_brand_src('admin_favicon_url', 'img/logo-mark.png');
}

function admin_logo_mark_src(): string
{
    $fav = trim((string) (app_settings_keyed()['admin_favicon_url'] ?? ''));
    if ($fav !== '') {
        return admin_brand_src('admin_favicon_url', 'img/logo-mark.png');
    }
    $logo = trim((string) (app_settings_keyed()['admin_logo_url'] ?? ''));
    if ($logo !== '') {
        return admin_brand_src('admin_logo_url', 'img/logo-mark.png');
    }
    return asset('img/logo-mark.png');
}

function auth_is_super_admin(?array $user = null): bool
{
    $user = $user ?? (function_exists('auth_user') ? auth_user() : null);
    return is_array($user) && ($user['role'] ?? '') === 'super_admin';
}

function media_brand_accept_attr(): string
{
    return '.jpg,.jpeg,.png,.gif,.webp,.avif,.bmp,.ico,image/jpeg,image/png,image/gif,image/webp,image/avif,image/bmp,image/x-icon';
}
