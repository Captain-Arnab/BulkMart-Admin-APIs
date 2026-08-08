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

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        if (!isset($map[$mime])) {
            throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
        }

        $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Failed to save uploaded image.');
        }

        return 'uploads/' . trim($subdir, '/') . '/' . $name;
    }
}
