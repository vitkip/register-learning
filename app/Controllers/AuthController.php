<?php

require_once 'app/Core/Controller.php';

/**
 * Authentication Controller
 */
class AuthController extends Controller {

    /**
     * Show login form
     */
    public function login() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [
            'title' => 'ເຂົ້າສູ່ລະບົບ'
        ]);
    }

    /**
     * Process login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }

        $this->validateCSRF();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $validator = new Validator($_POST);
        $validator->setRules([
            'username' => 'required|min:3',
            'password' => 'required|min:6'
        ]);

        if (!$validator->validate()) {
            Session::setMessage($validator->getFirstError(), 'error');
            $this->redirect('auth/login');
        }

        // Attempt login
        if ($this->auth->login($username, $password)) {
            Session::setMessage('ເຂົ້າສູ່ລະບົບສຳເລັດ', 'success');
            $this->redirect('dashboard');
        } else {
            Session::setMessage('ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ', 'error');
            $this->redirect('auth/login');
        }
    }

    /**
     * Logout
     */
    public function logout() {
        $this->auth->logout();
        Session::setMessage('ອອກຈາກລະບົບແລ້ວ', 'info');
        $this->redirect('auth/login');
    }
}