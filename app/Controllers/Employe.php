<?php

namespace App\Controllers;
use App\Models\EmployeModel;

class Employe extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function login() {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model = new EmployeModel();
        $user = $model->where('email', $email)->first();

        if (!$user || $user['password'] !== $password) {
            return redirect()->to('/')->with('error', 'Nom d’utilisateur ou mot de passe incorrect.');
        }

        session()->set('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return redirect()->to('/employe/dashboard')->with('success', 'Connexion réussie.');
    }
    
}
