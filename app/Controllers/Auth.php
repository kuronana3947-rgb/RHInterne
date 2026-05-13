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
        $db = db_connect();
        $year = (int) date('Y');
        $monthStart = date('Y-m-01 00:00:00');
        $today = date('Y-m-d');
        $search = trim((string) $this->request->getGet('search'));
        $departmentFilter = trim((string) $this->request->getGet('department'));

        $currentUser = $this->getAdminProfile($db);

        $employees = $db->table('employes e')
            ->select('e.id, e.nom, e.prenom, e.email, e.role, e.actif, e.date_embauche, d.nom AS dept_nom')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->orderBy('e.actif', 'DESC')
            ->orderBy('e.nom', 'ASC');

        if ($search !== '') {
            $employees->groupStart()
                ->like('e.nom', $search)
                ->orLike('e.prenom', $search)
                ->orLike('e.email', $search)
                ->orLike('d.nom', $search)
                ->groupEnd();
        }

        if ($departmentFilter !== '') {
            $employees->where('d.nom', $departmentFilter);
        }

        $employees = $employees->get()->getResultArray();

        $departments = $db->table('departements d')
            ->select('d.id, d.nom, d.description, COUNT(e.id) AS nb_employes')
            ->join('employes e', 'e.departement_id = d.id AND e.actif = 1', 'left')
            ->groupBy('d.id, d.nom, d.description')
            ->orderBy('d.nom', 'ASC')
            ->get()
            ->getResultArray();

        $typesConge = $db->table('types_conge')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $congesRows = $db->table('conges c')
            ->select('c.id, c.employe_id, c.type_conge_id, c.date_debut, c.date_fin, c.nb_jours, c.created_at, c.statut_id, e.nom, e.prenom, d.nom AS dept_nom, tc.libelle AS type_libelle, s.libelle AS statut_libelle')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->join('statuts s', 's.id = c.statut_id', 'left')
            ->orderBy('c.created_at', 'DESC')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        $soldesRows = $db->table('soldes s')
            ->select('s.id, s.employe_id, s.type_conge_id, s.annee, s.jours_attribues, s.jours_pris, e.nom, e.prenom, e.email, d.nom AS dept_nom, tc.libelle AS type_libelle')
            ->join('employes e', 'e.id = s.employe_id', 'left')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge tc', 'tc.id = s.type_conge_id', 'left')
            ->where('s.annee', $year)
            ->orderBy('e.nom', 'ASC')
            ->orderBy('tc.id', 'ASC')
            ->get()
            ->getResultArray();

        $absentsToday = $db->table('conges c')
            ->select('c.id, c.date_fin, c.type_conge_id, e.nom, e.prenom, d.nom AS dept_nom, tc.libelle AS type_libelle')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->where('c.statut_id', 2)
            ->where('c.date_debut <=', $today)
            ->where('c.date_fin >=', $today)
            ->orderBy('c.date_fin', 'ASC')
            ->get()
            ->getResultArray();

        $activeEmployees = count(array_filter($employees, static fn(array $employee): bool => (int) ($employee['actif'] ?? 0) === 1));
        $pendingRequests = count(array_filter($congesRows, static fn(array $row): bool => (int) ($row['statut_id'] ?? 0) === 1));
        $approvedThisMonth = count(array_filter($congesRows, static function (array $row) use ($monthStart): bool {
            return (int) ($row['statut_id'] ?? 0) === 2 && (string) ($row['created_at'] ?? '') >= $monthStart;
        }));
        $departmentsCount = count($departments);
        $absentTodayCount = count($absentsToday);

        $soldesByEmployee = [];
        foreach ($soldesRows as $row) {
            $employeeId = (int) ($row['employe_id'] ?? 0);
            $typeId = (int) ($row['type_conge_id'] ?? 0);
            $joursAttribues = (int) ($row['jours_attribues'] ?? 0);
            $joursPris = (int) ($row['jours_pris'] ?? 0);

            $row['jours_restants'] = $joursAttribues - $joursPris;
            $soldesByEmployee[$employeeId][$typeId] = $row;
        }

        $recentDemandes = array_slice($congesRows, 0, 5);
        $criticalSoldes = array_filter($soldesRows, static fn(array $row): bool => (int) ($row['jours_attribues'] ?? 0) - (int) ($row['jours_pris'] ?? 0) <= 5);
        usort($criticalSoldes, static fn(array $a, array $b): int => ((int) ($a['jours_attribues'] ?? 0) - (int) ($a['jours_pris'] ?? 0)) <=> ((int) ($b['jours_attribues'] ?? 0) - (int) ($b['jours_pris'] ?? 0)));
        $criticalSoldes = array_slice(array_values($criticalSoldes), 0, 3);

        foreach ($recentDemandes as &$demande) {
            $nom = trim((string) ($demande['nom'] ?? ''));
            $prenom = trim((string) ($demande['prenom'] ?? ''));
            $typeLabel = (string) ($demande['type_libelle'] ?? 'Congé annuel');
            $statutId = (int) ($demande['statut_id'] ?? 1);
            $typeKey = strtolower(trim($typeLabel));

            $demande['initiales'] = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
            $demande['avatar_class'] = $this->getDepartmentAvatarClass((string) ($demande['dept_nom'] ?? ''));
            $demande['type_class'] = $typeKey === 'congé maladie' || $typeKey === 'conge maladie' ? 't-maladie' : ($typeKey === 'congé spécial' || $typeKey === 'conge special' ? 't-special' : ($typeKey === 'congé sans solde' || $typeKey === 'conge sans solde' ? 't-sans-solde' : 't-annuel'));
            $demande['type_label'] = $typeLabel !== '' ? $typeLabel : 'Congé annuel';
            $demande['statut_label'] = $statutId === 2 ? 'approuvée' : ($statutId === 3 ? 'refusée' : ($statutId === 4 ? 'annulée' : 'en attente'));
            $demande['statut_class'] = $statutId === 2 ? 's-approuvee' : ($statutId === 3 ? 's-refusee' : ($statutId === 4 ? 's-annulee' : 's-attente'));
        }
        unset($demande);

        foreach ($employees as &$employee) {
            $employeeId = (int) ($employee['id'] ?? 0);
            $nom = trim((string) ($employee['nom'] ?? ''));
            $prenom = trim((string) ($employee['prenom'] ?? ''));
            $role = (string) ($employee['role'] ?? 'employe');
            $employee['initiales'] = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
            $employee['avatar_class'] = $this->getDepartmentAvatarClass((string) ($employee['dept_nom'] ?? ''));
            $employee['role_label'] = $role === 'admin' ? 'Administrateur' : ($role === 'rh' ? 'Responsable RH' : 'Employé');
            $employee['role_class'] = $role === 'admin' ? 't-special' : ($role === 'rh' ? 't-maladie' : 't-annuel');
            $employee['status_label'] = (int) ($employee['actif'] ?? 0) === 1 ? 'actif' : 'inactif';
            $employee['status_class'] = (int) ($employee['actif'] ?? 0) === 1 ? 's-approuvee' : 's-annulee';
            $employee['toggle_label'] = (int) ($employee['actif'] ?? 0) === 1 ? 'Désactiver' : 'Réactiver';
            $employee['toggle_icon'] = (int) ($employee['actif'] ?? 0) === 1 ? 'bi-slash-circle' : 'bi-arrow-counterclockwise';
            $employee['annual_balance'] = 0;

            if (isset($soldesByEmployee[$employeeId][1])) {
                $annualRow = $soldesByEmployee[$employeeId][1];
                $employee['annual_balance'] = (int) ($annualRow['jours_restants'] ?? ((int) ($annualRow['jours_attribues'] ?? 0) - (int) ($annualRow['jours_pris'] ?? 0)));
            }
        }
        unset($employee);

        foreach ($absentsToday as &$absent) {
            $absent['initiales'] = strtoupper(substr((string) ($absent['prenom'] ?? ''), 0, 1) . substr((string) ($absent['nom'] ?? ''), 0, 1));
            $absent['avatar_class'] = $this->getDepartmentAvatarClass((string) ($absent['dept_nom'] ?? ''));
            $absent['return_date'] = (string) ($absent['date_fin'] ?? '');
        }
        unset($absent);

        foreach ($criticalSoldes as &$solde) {
            $solde['jours_restants'] = (int) ($solde['jours_attribues'] ?? 0) - (int) ($solde['jours_pris'] ?? 0);
            $solde['initiales'] = strtoupper(substr((string) ($solde['prenom'] ?? ''), 0, 1) . substr((string) ($solde['nom'] ?? ''), 0, 1));
            $solde['avatar_class'] = $this->getDepartmentAvatarClass((string) ($solde['dept_nom'] ?? ''));
        }
        unset($solde);

        return view('admin/dashboard', [
            'title' => 'Tableau de bord administrateur',
            'currentUser' => $currentUser,
            'metrics' => [
                'activeEmployees' => $activeEmployees,
                'pendingRequests' => $pendingRequests,
                'approvedThisMonth' => $approvedThisMonth,
                'departmentsCount' => $departmentsCount,
                'absentTodayCount' => $absentTodayCount,
            ],
            'recentDemandes' => $recentDemandes,
            'absentsToday' => $absentsToday,
            'criticalSoldes' => $criticalSoldes,
            'employees' => $employees,
            'departments' => $departments,
            'typesConge' => $typesConge,
            'search' => $search,
            'departmentFilter' => $departmentFilter,
        ]);
    }

    public function adminEmployes(): string
    {
        $db = db_connect();
        $year = (int) date('Y');
        $search = trim((string) $this->request->getGet('search'));
        $departmentFilter = trim((string) $this->request->getGet('department'));
        $currentUser = $this->getAdminProfile($db);

        $employeesQuery = $db->table('employes e')
            ->select('e.id, e.nom, e.prenom, e.email, e.role, e.actif, e.date_embauche, d.nom AS dept_nom')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->orderBy('e.actif', 'DESC')
            ->orderBy('e.nom', 'ASC');

        if ($search !== '') {
            $employeesQuery->groupStart()
                ->like('e.nom', $search)
                ->orLike('e.prenom', $search)
                ->orLike('e.email', $search)
                ->orLike('d.nom', $search)
                ->groupEnd();
        }

        if ($departmentFilter !== '') {
            $employeesQuery->where('d.nom', $departmentFilter);
        }

        $employees = $employeesQuery->get()->getResultArray();

        $departments = $db->table('departements d')
            ->select('d.id, d.nom, d.description, COUNT(e.id) AS nb_employes')
            ->join('employes e', 'e.departement_id = d.id AND e.actif = 1', 'left')
            ->groupBy('d.id, d.nom, d.description')
            ->orderBy('d.nom', 'ASC')
            ->get()
            ->getResultArray();

        $typesConge = $db->table('types_conge')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $soldesRows = $db->table('soldes s')
            ->select('s.id, s.employe_id, s.type_conge_id, s.annee, s.jours_attribues, s.jours_pris, e.nom, e.prenom, d.nom AS dept_nom, tc.libelle AS type_libelle')
            ->join('employes e', 'e.id = s.employe_id', 'left')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge tc', 'tc.id = s.type_conge_id', 'left')
            ->where('s.annee', $year)
            ->orderBy('e.nom', 'ASC')
            ->orderBy('tc.id', 'ASC')
            ->get()
            ->getResultArray();

        $soldesByEmployee = [];
        foreach ($soldesRows as $row) {
            $employeeId = (int) ($row['employe_id'] ?? 0);
            $typeId = (int) ($row['type_conge_id'] ?? 0);
            $joursAttribues = (int) ($row['jours_attribues'] ?? 0);
            $joursPris = (int) ($row['jours_pris'] ?? 0);

            $row['jours_restants'] = $joursAttribues - $joursPris;
            $soldesByEmployee[$employeeId][$typeId] = $row;
        }

        foreach ($employees as &$employee) {
            $employeeId = (int) ($employee['id'] ?? 0);
            $nom = trim((string) ($employee['nom'] ?? ''));
            $prenom = trim((string) ($employee['prenom'] ?? ''));
            $role = (string) ($employee['role'] ?? 'employe');

            $employee['initiales'] = $this->buildInitials($prenom, $nom, 'AD');
            $employee['avatar_class'] = $this->getDepartmentAvatarClass((string) ($employee['dept_nom'] ?? ''));
            $employee['role_label'] = $role === 'admin' ? 'Administrateur' : ($role === 'rh' ? 'Responsable RH' : 'Employé');
            $employee['role_class'] = $role === 'admin' ? 't-special' : ($role === 'rh' ? 't-maladie' : 't-annuel');
            $employee['status_label'] = (int) ($employee['actif'] ?? 0) === 1 ? 'actif' : 'inactif';
            $employee['status_class'] = (int) ($employee['actif'] ?? 0) === 1 ? 's-approuvee' : 's-annulee';
            $employee['toggle_label'] = (int) ($employee['actif'] ?? 0) === 1 ? 'Désactiver' : 'Réactiver';
            $employee['toggle_icon'] = (int) ($employee['actif'] ?? 0) === 1 ? 'bi-slash-circle' : 'bi-arrow-counterclockwise';
            $employee['annual_balance'] = 0;

            if (isset($soldesByEmployee[$employeeId][1])) {
                $annualRow = $soldesByEmployee[$employeeId][1];
                $employee['annual_balance'] = (int) ($annualRow['jours_restants'] ?? ((int) ($annualRow['jours_attribues'] ?? 0) - (int) ($annualRow['jours_pris'] ?? 0)));
            }
        }
        unset($employee);

        return view('admin/employes', [
            'title' => 'Gestion des employés',
            'currentUser' => $currentUser,
            'employees' => $employees,
            'departments' => $departments,
            'typesConge' => $typesConge,
            'search' => $search,
            'departmentFilter' => $departmentFilter,
        ]);
    }

    public function storeAdminEmploye()
    {
        $employeModel = new EmployeModel();
        $data = [
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'nom' => trim((string) $this->request->getPost('nom')),
            'email' => trim((string) $this->request->getPost('email')),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => trim((string) $this->request->getPost('role')),
            'departement_id' => (int) $this->request->getPost('departement_id'),
            'date_embauche' => (string) $this->request->getPost('date_embauche'),
            'actif' => 1,
        ];

        if (!$employeModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $employeModel->errors()));
        }

        $employeId = (int) $employeModel->getInsertID();
        $year = (int) date('Y');
        $db = db_connect();

        $typesConge = $db->table('types_conge')
            ->select('id, jours_annuels')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($typesConge as $typeConge) {
            $db->table('soldes')->insert([
                'employe_id' => $employeId,
                'type_conge_id' => (int) $typeConge['id'],
                'annee' => $year,
                'jours_attribues' => (int) ($typeConge['jours_annuels'] ?? 0),
                'jours_pris' => 0,
            ]);
        }

        return redirect()->to('/admin/dashboard#page-admin-employes')->with('success', 'Employé créé avec ses soldes initiaux.');
    }

    public function toggleAdminEmploye($id)
    {
        $employeModel = new EmployeModel();
        $employee = $employeModel->find($id);

        if (!$employee) {
            return redirect()->to('/admin/dashboard#page-admin-employes')->with('error', 'Employé introuvable.');
        }

        $currentUserId = (int) ($this->getSessionUser()['id'] ?? 0);
        if ($currentUserId === (int) $id) {
            return redirect()->to('/admin/dashboard#page-admin-employes')->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $newStatus = (int) ($employee['actif'] ?? 0) === 1 ? 0 : 1;
        $employeModel->update($id, ['actif' => $newStatus]);

        $message = $newStatus === 1 ? 'Compte employé réactivé.' : 'Compte employé désactivé.';

        return redirect()->to('/admin/dashboard#page-admin-employes')->with('success', $message);
    }

    public function rhDashboard(): string
    {
        return view('role_dashboard', [
            'title' => 'Tableau de bord RH',
            'roleTitle' => 'Responsable RH',
            'roleDescription' => 'Espace de démonstration pour le rôle RH.',
        ]);
    }

    private function getAdminProfile($db): array
    {
        $sessionUser = $this->getSessionUser();
        $userId = (int) ($sessionUser['id'] ?? 0);

        if ($userId > 0) {
            $details = $db->table('employes e')
                ->select('e.nom, e.prenom, e.role, d.nom AS departement')
                ->join('departements d', 'd.id = e.departement_id', 'left')
                ->where('e.id', $userId)
                ->get()
                ->getRowArray();

            if ($details) {
                $prenom = trim((string) ($details['prenom'] ?? ''));
                $nom = trim((string) ($details['nom'] ?? ''));

                return [
                    'nom' => $nom !== '' ? $nom : 'Administrateur',
                    'prenom' => $prenom,
                    'departement' => (string) ($details['departement'] ?? ''),
                    'initiales' => $this->buildInitials($prenom, $nom, 'AD'),
                    'role' => (string) ($details['role'] ?? 'admin'),
                ];
            }
        }

        return [
            'nom' => 'Administrateur',
            'prenom' => '',
            'departement' => '',
            'initiales' => 'AD',
            'role' => 'admin',
        ];
    }

    private function getSessionUser(): array
    {
        return session()->get('user') ?? [];
    }

    private function buildInitials(string $prenom, string $nom, string $default = 'AD'): string
    {
        $initials = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));

        return $initials !== '' ? $initials : $default;
    }

    private function getDepartmentAvatarClass(string $departmentName): string
    {
        $normalized = strtolower(trim($departmentName));

        if ($normalized === 'ressources humaines' || $normalized === 'rh') {
            return 'av-blue';
        }

        if ($normalized === 'comptabilite' || $normalized === 'finance') {
            return 'av-amber';
        }

        if ($normalized === 'administration' || $normalized === 'admin') {
            return 'av-slate';
        }

        return 'av-green';
    }
}