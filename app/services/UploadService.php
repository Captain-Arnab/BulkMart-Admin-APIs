<?php

class UploadService
{
    public const MAX_BYTES = 5242880;

    public static function storeImage(array $file, string $subdir, int $maxBytes = self::MAX_BYTES, ?array $allowedExt = null): ?string
    {
        $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('File is larger than the server upload limit. Use a file under 5MB.');
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload failed.');
        }
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new RuntimeException('File must be 5MB or smaller.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $ext = self::detectImageExtension($tmp);
        if ($ext === null || ($allowedExt !== null && !in_array($ext, $allowedExt, true))) {
            throw new RuntimeException(
                $allowedExt
                    ? 'Use a JPG, PNG, GIF, WEBP, AVIF, BMP, or ICO image.'
                    : 'Only JPG, JPEG, PNG, GIF, WEBP, AVIF, BMP, or PDF files are allowed.'
            );
        }

        $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Failed to save uploaded file.');
        }

        return 'uploads/' . trim($subdir, '/') . '/' . $name;
    }

    /**
     * Prefer file signatures over finfo. XAMPP/Windows libmagic often
     * mislabels WebP/AVIF (ISO/RIFF containers).
     */
    public static function detectImageExtension(string $path): ?string
    {
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $head = file_get_contents($path, false, null, 0, 32);
        if (!is_string($head) || strlen($head) < 4) {
            return null;
        }

        if (str_starts_with($head, "\x00\x00\x01\x00")) {
            return 'ico';
        }
        if (strlen($head) < 8) {
            return null;
        }

        if (str_starts_with($head, "\xFF\xD8\xFF")) {
            return 'jpg';
        }
        if (str_starts_with($head, "\x89PNG\r\n\x1A\n")) {
            return 'png';
        }
        if (str_starts_with($head, 'GIF87a') || str_starts_with($head, 'GIF89a')) {
            return 'gif';
        }
        if (str_starts_with($head, 'BM')) {
            return 'bmp';
        }
        if (str_starts_with($head, '%PDF')) {
            return 'pdf';
        }
        if (strlen($head) >= 12 && substr($head, 0, 4) === 'RIFF' && substr($head, 8, 4) === 'WEBP') {
            return 'webp';
        }
        if (strlen($head) >= 12 && substr($head, 4, 4) === 'ftyp') {
            $brand = strtolower(substr($head, 8, 4));
            if (in_array($brand, ['avif', 'avis', 'avio'], true) || str_contains(strtolower($head), 'avif')) {
                return 'avif';
            }
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = strtolower(trim(explode(';', (string) $finfo->file($path))[0]));
        return match ($mime) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
            'image/png', 'image/x-png', 'image/apng' => 'png',
            'image/gif' => 'gif',
            'image/webp', 'image/x-webp' => 'webp',
            'image/avif' => 'avif',
            'image/bmp', 'image/x-ms-bmp', 'image/x-bmp' => 'bmp',
            'image/x-icon', 'image/vnd.microsoft.icon', 'image/ico' => 'ico',
            'application/pdf', 'application/x-pdf' => 'pdf',
            default => null,
        };
    }
}
