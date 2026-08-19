<?php
/**
 * Customer website entry (XAMPP / project-root document root).
 *
 * Admin panel and API are unchanged under /public.
 * Prefer pointing the vhost document root at this project root so:
 *   /                  → this file / web/
 *   /public/dashboard  → admin
 *   /public/api/v1/*   → customer API
 */
chdir(__DIR__ . '/web');
require __DIR__ . '/web/index.php';
