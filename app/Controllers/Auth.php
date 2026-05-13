<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class Auth extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        $demoAccounts = $db->table('employes')
            ->select('role, email, password')
            ->whereIn('role', ['admin', 'rh', 'employe'])
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $demoAccounts = array_map(static function (array $account): array {
            $role = (string) ($account['role'] ?? 'employe');

            if ($role === 'admin') {
                $label = 'Administrateur';
                $icon = 'bi-shield-check';
            } elseif ($role === 'rh') {
                $label = 'Responsable RH';
                $icon = 'bi-person-check';
            } else {
                $label = 'Employé';
                $icon = 'bi-person';
            }

            return [
                'label' => $label,
                'icon' => $icon,
                'email' => (string) ($account['email'] ?? ''),
                'password' => (string) ($account['password'] ?? ''),
            ];
        }, $demoAccounts);

        return view('auth/login', [
            'demoAccounts' => $demoAccounts,
        ]);
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $model = new EmployeModel();
        $user = $model->where('email', $email)->first();

        $passwordIsValid = $user && (
            password_verify($password, (string) $user['password']) || $user['password'] === $password
        );

        if (!$passwordIsValid) {
            return redirect()->to('/auth/login')->with('error', 'Nom d’utilisateur ou mot de passe incorrect.');
        }

        session()->set('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return match ($user['role']) {
            'admin' => redirect()->to('/admin/dashboard'),
            'rh' => redirect()->to('/rh/dashboard'),
            default => redirect()->to('/employe/dashboard'),
        };
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/auth/login')->with('success', 'Vous avez été déconnecté.');
    }

    public function adminDashboard(): string
    {
        return view('role_dashboard', [
            'title' => 'Tableau de bord administrateur',
            'roleTitle' => 'Administrateur',
            'roleDescription' => 'Espace de démonstration pour le rôle administrateur.',
        ]);
    }

    public function rhDashboard(): string
    {
        return view('role_dashboard', [
            'title' => 'Tableau de bord RH',
            'roleTitle' => 'Responsable RH',
            'roleDescription' => 'Espace de démonstration pour le rôle RH.',
        ]);
    }
}