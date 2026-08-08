<?php

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->view('settings/index', [
            'title'    => 'Settings',
            'settings' => (new AppSetting())->allKeyed(),
            'success'  => flash('success'),
            'error'    => flash('error'),
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
        $model = new AppSetting();
        $model->set('support_phone', trim((string) ($_POST['support_phone'] ?? '')));
        $model->set('support_email', trim((string) ($_POST['support_email'] ?? '')));
        $model->set('company_name', trim((string) ($_POST['company_name'] ?? '')));
        flash('success', 'App settings saved.');
        redirect('settings');
    }
}
