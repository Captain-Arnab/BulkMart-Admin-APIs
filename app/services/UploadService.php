<?php

class UploadService
{
    public static function storeImage(array $file, string $subdir, int $maxBytes = 2097152): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Image upload failed.');
        }
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new RuntimeException('Image must be 2MB or smaller.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $ext = self::detectImageExtension($tmp);
        if ($ext === null) {
            throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
        }

        $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Failed to save uploaded image.');
        }

        return 'uploads/' . trim($subdir, '/') . '/' . $name;
    }

    /**
     * Prefer file signatures over finfo. XAMPP/Windows libmagic often
     * reports WebP (RIFF container) as application/octet-stream or audio/x-riff.
     */
    public static function detectImageExtension(string $path): ?string
    {
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $head = file_get_contents($path, false, null, 0, 16);
        if (!is_string($head) || strlen($head) < 12) {
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
        if (substr($head, 0, 4) === 'RIFF' && substr($head, 8, 4) === 'WEBP') {
            return 'webp';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = strtolower(trim(explode(';', (string) $finfo->file($path))[0]));
        return match ($mime) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
            'image/png', 'image/x-png' => 'png',
            'image/gif' => 'gif',
            'image/webp', 'image/x-webp' => 'webp',
            default => null,
        };
    }
}
