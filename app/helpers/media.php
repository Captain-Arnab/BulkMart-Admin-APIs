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
