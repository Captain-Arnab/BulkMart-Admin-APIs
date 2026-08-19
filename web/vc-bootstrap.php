<?php
/**
 * Light bootstrap so the customer website can read shared app settings
 * (logo / favicon) without loading the admin router.
 */
if (!defined('APP_ROOT')) {
    require_once dirname(__DIR__) . '/app/config/db.php';
    require_once dirname(__DIR__) . '/app/core/Model.php';
    require_once dirname(__DIR__) . '/app/models/AppSetting.php';
}
