<?php

class RoleController extends Controller
{
    public function index(): void
    {
        $this->view('roles/index', [
            'title'   => 'Roles & Sub-Admins',
            'admins'  => (new AdminUser())->allWithModuleCounts(),
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function create(): void
    {
        $this->view('roles/form', [
            'title'   => 'Create Admin User',
            'admin'   => null,
            'modules' => [],
            'error'   => flash('error'),
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validated(true);
            $model = new AdminUser();
            if ($model->findByEmail($data['email'])) {
                throw new InvalidArgumentException('Email already in use.');
            }
            $id = $model->createAdmin($data);
            if ($data['role_type'] === 'sub_admin') {
                $model->syncModules($id, $data['module_keys']);
            } elseif ($data['role_type'] === 'delivery_manager') {
                $model->syncModules($id, ['delivery']);
            }
            flash('success', 'Admin user created.');
            redirect('roles');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('roles/create');
        }
    }

    public function edit(string $id): void
    {
        $model = new AdminUser();
        $admin = $model->find((int) $id);
        if (!$admin) {
            flash('error', 'Admin not found.');
            redirect('roles');
        }
        $this->view('roles/form', [
            'title'   => 'Edit Admin User',
            'admin'   => $admin,
            'modules' => $model->moduleKeys((int) $id),
            'error'   => flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $model = new AdminUser();
        $admin = $model->find((int) $id);
        if (!$admin) {
            flash('error', 'Admin not found.');
            redirect('roles');
        }
        try {
            // Prevent demoting/deactivating the last super admin casually — soft guard
            if ($admin['role_type'] === 'super_admin' && ($_POST['role_type'] ?? '') !== 'super_admin') {
                $count = (int) db()->query("SELECT COUNT(*) FROM admin_users WHERE role_type='super_admin' AND is_active=1")->fetchColumn();
                if ($count <= 1) {
                    throw new InvalidArgumentException('Cannot demote the only active Super Admin.');
                }
            }
            $data = $this->validated(false);
            $password = trim((string) ($_POST['password'] ?? ''));
            $model->updateAdmin((int) $id, $data, $password !== '' ? $password : null);
            if ($data['role_type'] === 'sub_admin') {
                $model->syncModules((int) $id, $data['module_keys']);
            } elseif ($data['role_type'] === 'delivery_manager') {
                $model->syncModules((int) $id, ['delivery']);
            } else {
                $model->syncModules((int) $id, []);
            }
            flash('success', 'Admin user updated.');
            redirect('roles');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('roles/' . (int) $id . '/edit');
        }
    }

    public function toggleActive(string $id): void
    {
        $model = new AdminUser();
        $admin = $model->find((int) $id);
        if (!$admin) {
            flash('error', 'Admin not found.');
            redirect('roles');
        }
        if ((int) $admin['id'] === (int) auth_user()['id']) {
            flash('error', 'You cannot deactivate your own account.');
            redirect('roles');
        }
        $active = !((int) $admin['is_active'] === 1);
        if (!$active && $admin['role_type'] === 'super_admin') {
            $count = (int) db()->query("SELECT COUNT(*) FROM admin_users WHERE role_type='super_admin' AND is_active=1")->fetchColumn();
            if ($count <= 1) {
                flash('error', 'Cannot deactivate the only active Super Admin.');
                redirect('roles');
            }
        }
        $model->setActive((int) $id, $active);
        flash('success', $active ? 'Admin activated.' : 'Admin deactivated.');
        redirect('roles');
    }

    private function validated(bool $requirePassword): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $role = trim((string) ($_POST['role_type'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $modules = $_POST['modules'] ?? [];
        if (!is_array($modules)) {
            $modules = [];
        }

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Valid name and email are required.');
        }
        if (!in_array($role, ['super_admin', 'sub_admin', 'delivery_manager'], true)) {
            throw new InvalidArgumentException('Invalid role type.');
        }
        if ($requirePassword && strlen($password) < 6) {
            throw new InvalidArgumentException('Password must be at least 6 characters.');
        }
        if (!$requirePassword && $password !== '' && strlen($password) < 6) {
            throw new InvalidArgumentException('Password must be at least 6 characters.');
        }

        return [
            'name'        => $name,
            'email'       => $email,
            'password'    => $password,
            'role_type'   => $role,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'module_keys' => array_values(array_map('strval', $modules)),
        ];
    }
}
