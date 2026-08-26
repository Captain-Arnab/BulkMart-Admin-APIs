<?php

class SettingsController extends Controller
{
    public function index(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        $this->view('settings/index', [
            'title'     => 'Settings',
            'settings'  => (new AppSetting())->allKeyed(),
            'canBrand'  => auth_is_super_admin(),
            'success'   => flash('success'),
            'error'     => flash('error'),
        ]);
    }

    public function updatePassword(): void
    {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        if (strlen($new) < 6) {
            flash('error', 'New password must be at least 6 characters.');
            redirect('settings');
        }
        if ($new !== $confirm) {
            flash('error', 'New password confirmation does not match.');
            redirect('settings');
        }
        $user = auth_user();
        $admin = (new AdminUser())->find((int) $user['id']);
        if (!$admin || !password_verify($current, $admin['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('settings');
        }
        (new AdminUser())->updatePassword((int) $user['id'], $new);
        flash('success', 'Password updated.');
        redirect('settings');
    }

    public function updateApp(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            redirect('settings');
        }

        $posted = array_filter(array_keys($_POST), static fn (string $k): bool => !str_starts_with($k, '_'));
        if ($posted === []) {
            flash('error', 'No settings data received. If this keeps happening, set app.base_url in config.local.php and retry.');
            redirect('settings');
        }

        try {
            $model = new AppSetting();
            $model->set('support_phone', trim((string) ($_POST['support_phone'] ?? '')));
            $model->set('support_email', trim((string) ($_POST['support_email'] ?? '')));
            $model->set('company_name', trim((string) ($_POST['company_name'] ?? '')));
            $requireKyc = trim((string) ($_POST['require_kyc_approved'] ?? '0'));
            $model->set('require_kyc_approved', in_array(strtolower($requireKyc), ['1', 'true', 'yes', 'on'], true) ? '1' : '0');
            flash('success', 'App settings saved.');
        } catch (Throwable $e) {
            flash('error', APP_DEBUG ? $e->getMessage() : 'Could not save app settings. Please try again.');
        }
        redirect('settings');
    }

    public function updateBranding(): void
    {
        $model = new AppSetting();
        $exts = ['jpg', 'png', 'gif', 'webp', 'avif', 'bmp', 'ico'];
        try {
            $changed = false;
            if (!empty($_POST['remove_admin_logo'])) {
                $model->set('admin_logo_url', '');
                $changed = true;
            } elseif (!empty($_FILES['admin_logo']['name'])) {
                $url = UploadService::storeImage($_FILES['admin_logo'], 'branding', UploadService::MAX_BYTES, $exts);
                if ($url) {
                    $model->set('admin_logo_url', $url);
                    $changed = true;
                }
            }
            if (!empty($_POST['remove_admin_favicon'])) {
                $model->set('admin_favicon_url', '');
                $changed = true;
            } elseif (!empty($_FILES['admin_favicon']['name'])) {
                $url = UploadService::storeImage($_FILES['admin_favicon'], 'branding', UploadService::MAX_BYTES, $exts);
                if ($url) {
                    $model->set('admin_favicon_url', $url);
                    $changed = true;
                }
            }
            if (!$changed) {
                flash('error', 'Choose a logo or favicon file to upload, or tick restore default.');
                redirect('settings');
            }
            $model->set('admin_brand_rev', (string) time());
            flash('success', 'Logo and favicon updated for admin and website.');
            redirect('settings');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('settings');
        }
    }
}
