<?php

require_once APP_PATH . '/middleware/auth.php';

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (auth_check()) {
            redirect(auth_home_path());
        }
        $this->view('auth/login', [
            'title' => 'Login',
            'error' => flash('error'),
        ], 'layouts/guest');
    }

    public function login(): void
    {
        $identity = trim((string) ($_POST['identity'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($identity === '' || $password === '') {
            flash('error', 'Email/username and password are required.');
            redirect('login');
        }

        if (!attempt_login($identity, $password)) {
            flash('error', 'Invalid credentials. Please try again.');
            redirect('login');
        }

        redirect(auth_home_path());
    }

    public function logout(): void
    {
        auth_logout();
        auth_start_session();
        flash('error', 'You have been signed out.');
        redirect('login');
    }
}
