<?php

namespace App\Controllers;

use Framework\Controller;
use Framework\Validation;
use Framework\Session;
use Framework\Router;

class UserController extends Controller
{
    private $allowedFields = ['name', 'email', 'city', 'state', 'password', 'password_confirmation'];
    private $requiredFields = ['name', 'email', 'password'];

    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        loadView('users/login');
    }

    public function authenticate()
    {
        $errors = [];

        $userData = array_intersect_key($_POST, array_flip(['email', 'password']));

        // Lookup the user
        $user = $this->db->query('SELECT * FROM users WHERE email = :email', [
            'email' => $userData['email']
        ])->fetch();

        if ($user && $this->verifyPassword($userData['password'], $user->password)) {
            Session::set('user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->city,
                'state' => $user->state
            ]);

            redirect('/');
            return;
        } else {
            $errors['login'] = 'Invalid credentials';
        }

        loadView('users/login', [
            'errors' => $errors ?? [],
            'user' => (object)($userData ?? [])
        ]);
    }

    public function logout()
    {
        Session::clearAll();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain']);

        redirect('/');
    }

    public function create()
    {
        loadView('users/create');
    }

    public function store()
    {
        $errors = [];

        $newUserData = array_intersect_key($_POST, array_flip($this->allowedFields));

        if (!Validation::string($newUserData['name'], 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        if (!Validation::email($newUserData['email'])) {
            $errors['email'] = 'Invalid email address';
        }

        if (!Validation::string($newUserData['password'], 8, 50)) {
            $errors['password'] = 'Password must be between 8 and 50 characters';
        }

        if (!Validation::match($newUserData['password'], $newUserData['password_confirmation'])) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        foreach ($this->requiredFields as $field) {
            if (empty($newUserData[$field]) || !Validation::string($newUserData[$field])) {
                $errors[$field] = ucfirst($field . ' is required');
            }
        }

        // Only check the database if no validation errors
        if (empty($errors)) {
            if ($this->isEmailUnique($newUserData['email'])) {
                $errors['email'] = 'That email already exists';
            }
        }

        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => (object)$newUserData
            ]);
            return;
        }

        $userId = $this->createRecord('users', [
            'name' => $newUserData['name'],
            'email' => $newUserData['email'],
            'city' => $newUserData['city'],
            'state' => $newUserData['state'],
            'password' => $this->hashPassword($newUserData['password'])
        ]);

        Session::set('user', [
            'id' => $userId,
            'name' => $newUserData['name'],
            'email' => $newUserData['email'],
            'city' => $newUserData['city'],
            'state' => $newUserData['state']
        ]);

        redirect('/');
    }

    private function isEmailUnique($email = '')
    {
        $user = $this->db->query('SELECT * from users WHERE email = :email', [
            'email' => $email
        ])->fetch();

        return !empty($user);
    }

    private function hashPassword($password = '')
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function verifyPassword($password = '', $hash = '')
    {
        return password_verify($password, $hash);
    }
}
