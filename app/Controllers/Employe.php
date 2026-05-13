<?php

namespace App\Controllers;
use App\Models\EmployeModel;

class Employe extends BaseController
{
    public function index(): string
    {
        return view('auth/login');
    }

    public function demandes(): string
    {
        $employeId = $this->getAuthenticatedEmployeId();
        $context = $this->loadEmployeContext($employeId);
        $filterStatut = strtolower(trim((string) $this->request->getGet('statut')));

        $query = db_connect()->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.commentaire_rh, tc.libelle AS type_libelle, s.libelle AS statut_libelle')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->join('statuts s', 's.id = c.statut_id', 'left')
            ->where('c.employe_id', $employeId)
            ->orderBy('c.created_at', 'DESC')
            ->orderBy('c.id', 'DESC');

        if ($filterStatut !== '' && array_key_exists($filterStatut, ['en_attente' => true, 'approuve' => true, 'refuse' => true, 'annule' => true])) {
            $query->where('s.libelle', $filterStatut);
        }

        $demandes = $query->get()->getResultArray();

        foreach ($demandes as &$demande) {
            $typeKey = $this->mapTypeCongeKey((string) ($demande['type_libelle'] ?? ''));
            $statutKey = (string) ($demande['statut_libelle'] ?? 'en_attente');

            $demande['type_label'] = $typeKey === 'maladie' ? 'Maladie' : ($typeKey === 'special' ? 'Spécial' : 'Annuel');
            $demande['type_class'] = $typeKey === 'maladie' ? 't-maladie' : ($typeKey === 'special' ? 't-special' : 't-annuel');
            $demande['statut_label'] = $statutKey === 'approuve' ? 'approuvée' : ($statutKey === 'refuse' ? 'refusée' : ($statutKey === 'annule' ? 'annulée' : 'en attente'));
            $demande['statut_class'] = $statutKey === 'approuve' ? 's-approuvee' : ($statutKey === 'refuse' ? 's-refusee' : ($statutKey === 'annule' ? 's-annulee' : 's-attente'));
            $demande['can_cancel'] = $statutKey === 'en_attente';
            $demande['commentaire_rh'] = trim((string) ($demande['commentaire_rh'] ?? ''));
            $demande['nb_jours'] = (int) ($demande['nb_jours'] ?? 0);
        }
        unset($demande);

        return view('employe/index', [
            'title' => 'Mes demandes de congé',
            'employe' => $context['employe'],
            'demandes' => $demandes,
            'statutsOptions' => [
                'en_attente' => 'En attente',
                'approuve' => 'Approuvée',
                'refuse' => 'Refusée',
                'annule' => 'Annulée',
            ],
            'selectedStatut' => $filterStatut,
            'flashSuccess' => session()->getFlashdata('success'),
            'flashError' => session()->getFlashdata('error'),
        ]);
    }

    public function cancelDemande()
    {
        $employeId = $this->getAuthenticatedEmployeId();
        $demandeId = (int) $this->request->getPost('demande_id');

        if ($employeId <= 0) {
            return redirect()->to('/auth/login')->with('error', 'Vous devez être connecté pour annuler une demande.');
        }

        $db = db_connect();
        $demande = $db->table('conges c')
            ->select('c.id, s.libelle AS statut_libelle')
            ->join('statuts s', 's.id = c.statut_id', 'left')
            ->where('c.id', $demandeId)
            ->where('c.employe_id', $employeId)
            ->get()
            ->getRowArray();

        if (!$demande) {
            return redirect()->to('/employe/conges')->with('error', 'Demande introuvable.');
        }

        if (($demande['statut_libelle'] ?? '') !== 'en_attente') {
            return redirect()->to('/employe/conges')->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        $statutAnnule = $db->table('statuts')
            ->select('id')
            ->where('libelle', 'annule')
            ->get()
            ->getRowArray();

        if (!$statutAnnule) {
            return redirect()->to('/employe/conges')->with('error', 'Le statut annulé est introuvable.');
        }

        $db->table('conges')
            ->where('id', $demandeId)
            ->where('employe_id', $employeId)
            ->update(['statut_id' => (int) $statutAnnule['id']]);

        return redirect()->to('/employe/conges')->with('success', 'La demande a été annulée.');
    }

    public function create(): string
    {
        $employeId = $this->getAuthenticatedEmployeId();
        $context = $this->loadEmployeContext($employeId);

        return view('employe/create', [
            'title' => 'Nouvelle demande de congé',
            'employe' => $context['employe'],
            'annee' => $context['annee'],
            'soldes' => $context['soldes'],
            'typesConge' => $context['typesConge'],
            'flashError' => session()->getFlashdata('error'),
            'flashSuccess' => session()->getFlashdata('success'),
            'oldTypeCongeId' => old('type_conge_id'),
            'oldDateDebut' => old('date_debut'),
            'oldDateFin' => old('date_fin'),
            'oldMotif' => old('motif'),
            'calculatedDays' => old('nb_jours'),
        ]);
    }

    public function store()
    {
        $employeId = $this->getAuthenticatedEmployeId();

        if ($employeId <= 0) {
            return redirect()->to('/auth/login')->with('error', 'Vous devez être connecté pour soumettre une demande.');
        }

        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = (string) $this->request->getPost('date_debut');
        $dateFin = (string) $this->request->getPost('date_fin');
        $motif = trim((string) $this->request->getPost('motif'));

        $db = db_connect();
        $typeConge = $db->table('types_conge')->where('id', $typeCongeId)->get()->getRowArray();

        if (!$typeConge) {
            return redirect()->back()->withInput()->with('error', 'Le type de congé sélectionné est invalide.');
        }

        if ($dateDebut === '' || $dateFin === '') {
            return redirect()->back()->withInput()->with('error', 'Les dates de début et de fin sont obligatoires.');
        }

        $start = new \DateTimeImmutable($dateDebut);
        $end = new \DateTimeImmutable($dateFin);

        if ($end < $start) {
            return redirect()->back()->withInput()->with('error', 'La date de fin doit être postérieure à la date de début.');
        }

        $nbJours = $this->countCalendarDays($start, $end);
        $currentYear = (int) date('Y');

        $soldeRow = $db->table('soldes')
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $currentYear)
            ->get()
            ->getRowArray();

        $joursRestants = (int) ($soldeRow['jours_attribues'] ?? 0) - (int) ($soldeRow['jours_pris'] ?? 0);

        if ($joursRestants < $nbJours) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour ce type de congé.');
        }

        $statutEnAttente = $db->table('statuts')
            ->select('id')
            ->where('libelle', 'en_attente')
            ->get()
            ->getRowArray();

        if (!$statutEnAttente) {
            return redirect()->back()->withInput()->with('error', 'Le statut de demande est introuvable.');
        }

        $db->transStart();

        $db->table('conges')->insert([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif !== '' ? $motif : null,
            'statut_id' => (int) $statutEnAttente['id'],
            'commentaire_rh' => null,
            'traite_par' => null,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Impossible d’enregistrer la demande.');
        }

        return redirect()->to('/employe/conge/create')->with('success', 'Votre demande de congé a bien été envoyée.');
    }

    public function dashboard(): string
    {
        $employeId = $this->getAuthenticatedEmployeId();
        $context = $this->loadEmployeContext($employeId);

        return view('employe/dashboard', [
            'employe' => $context['employe'],
            'annee' => $context['annee'],
            'statuts' => $context['statuts'],
            'soldes' => $context['soldes'],
            'demandes' => $context['demandes'],
            'totalDemandes' => array_sum($context['statuts']),
            'flashSuccess' => session()->getFlashdata('success'),
        ]);
    }

    private function getAuthenticatedEmployeId(): int
    {
        $sessionUser = session()->get('user') ?? [];

        return (int) ($sessionUser['id'] ?? 0);
    }

    private function loadEmployeContext(int $employeId): array
    {
        $db = db_connect();
        $annee = (int) date('Y');

        $employe = [
            'nom' => 'Employé',
            'prenom' => '',
            'role' => 'employe',
            'departement' => '',
            'initiales' => 'E',
        ];

        if ($employeId > 0) {
            $details = $db->table('employes e')
                ->select('e.id, e.nom, e.prenom, e.role, d.nom AS departement')
                ->join('departements d', 'd.id = e.departement_id', 'left')
                ->where('e.id', $employeId)
                ->get()
                ->getRowArray();

            if ($details) {
                $prenom = trim((string) ($details['prenom'] ?? ''));
                $nom = trim((string) ($details['nom'] ?? ''));

                $employe = [
                    'nom' => $nom !== '' ? $nom : 'Employé',
                    'prenom' => $prenom,
                    'role' => $details['role'] ?? 'employe',
                    'departement' => $details['departement'] ?? '',
                    'initiales' => $this->buildInitials($prenom, $nom),
                ];
            }
        }

        $soldes = [
            'annuel' => ['label' => 'Congé annuel', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
            'maladie' => ['label' => 'Congé maladie', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
            'special' => ['label' => 'Congé spécial', 'attribues' => 0, 'pris' => 0, 'restants' => 0, 'progress' => 0],
        ];

        $typesCongeRows = $db->table('types_conge')->orderBy('id', 'ASC')->get()->getResultArray();
        $typesConge = [];

        foreach ($typesCongeRows as $row) {
            $typeKey = $this->mapTypeCongeKey((string) ($row['libelle'] ?? ''));
            if ($typeKey === null) {
                continue;
            }

            $typesConge[] = [
                'id' => (int) ($row['id'] ?? 0),
                'libelle' => (string) ($row['libelle'] ?? ''),
                'jours_annuels' => (int) ($row['jours_annuels'] ?? 0),
                'deductible' => (int) ($row['deductible'] ?? 0),
                'key' => $typeKey,
            ];
        }

        if ($employeId > 0) {
            $soldesRows = $db->table('soldes s')
                ->select('tc.libelle, COALESCE(s.jours_attribues, 0) AS jours_attribues, COALESCE(s.jours_pris, 0) AS jours_pris')
                ->join('types_conge tc', 'tc.id = s.type_conge_id', 'left')
                ->where('s.employe_id', $employeId)
                ->where('s.annee', $annee)
                ->get()
                ->getResultArray();

            foreach ($soldesRows as $row) {
                $key = $this->mapTypeCongeKey((string) ($row['libelle'] ?? ''));

                if ($key === null || !isset($soldes[$key])) {
                    continue;
                }

                $soldes[$key]['attribues'] = (int) ($row['jours_attribues'] ?? 0);
                $soldes[$key]['pris'] = (int) ($row['jours_pris'] ?? 0);
            }
        }

        foreach ($soldes as &$solde) {
            $solde['restants'] = max(0, $solde['attribues'] - $solde['pris']);
            $solde['progress'] = $solde['attribues'] > 0 ? (int) round(($solde['pris'] / $solde['attribues']) * 100) : 0;
            $solde['progress'] = min(100, max(0, $solde['progress']));
        }
        unset($solde);

        $statuts = [
            'en_attente' => 0,
            'approuve' => 0,
            'refuse' => 0,
        ];

        if ($employeId > 0) {
            $statutRows = $db->table('conges c')
                ->select('s.libelle, COUNT(*) AS total')
                ->join('statuts s', 's.id = c.statut_id', 'left')
                ->where('c.employe_id', $employeId)
                ->groupBy('s.libelle')
                ->get()
                ->getResultArray();

            foreach ($statutRows as $row) {
                $libelle = (string) ($row['libelle'] ?? '');

                if (array_key_exists($libelle, $statuts)) {
                    $statuts[$libelle] = (int) ($row['total'] ?? 0);
                }
            }
        }

        $demandes = [];
        if ($employeId > 0) {
            $demandes = $db->table('conges c')
                ->select('c.date_debut, c.date_fin, c.nb_jours, tc.libelle AS type_libelle, s.libelle AS statut_libelle')
                ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
                ->join('statuts s', 's.id = c.statut_id', 'left')
                ->where('c.employe_id', $employeId)
                ->orderBy('c.created_at', 'DESC')
                ->orderBy('c.id', 'DESC')
                ->limit(3)
                ->get()
                ->getResultArray();
        }

        foreach ($demandes as &$demande) {
            $typeKey = $this->mapTypeCongeKey((string) ($demande['type_libelle'] ?? ''));
            $statutKey = (string) ($demande['statut_libelle'] ?? 'en_attente');

            if ($typeKey === 'maladie') {
                $demande['type_label'] = 'Maladie';
                $demande['type_class'] = 't-maladie';
            } elseif ($typeKey === 'special') {
                $demande['type_label'] = 'Spécial';
                $demande['type_class'] = 't-special';
            } else {
                $demande['type_label'] = 'Annuel';
                $demande['type_class'] = 't-annuel';
            }

            if ($statutKey === 'approuve') {
                $demande['statut_label'] = 'approuvée';
                $demande['statut_class'] = 's-approuvee';
            } elseif ($statutKey === 'refuse') {
                $demande['statut_label'] = 'refusée';
                $demande['statut_class'] = 's-refusee';
            } else {
                $demande['statut_label'] = 'en attente';
                $demande['statut_class'] = 's-attente';
            }

            $demande['can_cancel'] = $statutKey === 'en_attente';
            $demande['nb_jours'] = (int) ($demande['nb_jours'] ?? 0);
        }
        unset($demande);

        return [
            'annee' => $annee,
            'employe' => $employe,
            'soldes' => $soldes,
            'typesConge' => $typesConge,
            'statuts' => $statuts,
            'demandes' => $demandes,
        ];
    }

    private function countCalendarDays(\DateTimeImmutable $start, \DateTimeImmutable $end): int
    {
        return $start->diff($end)->days + 1;
    }

    private function buildInitials(string $prenom, string $nom): string
    {
        $initials = strtoupper(substr(trim($prenom), 0, 1) . substr(trim($nom), 0, 1));

        return $initials !== '' ? $initials : 'E';
    }

    private function mapTypeCongeKey(string $libelle): ?string
    {
        $normalized = strtolower(trim($libelle));

        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, 'annuel')) {
            return 'annuel';
        }

        if (str_contains($normalized, 'maladie')) {
            return 'maladie';
        }

        if (str_contains($normalized, 'spécial') || str_contains($normalized, 'special')) {
            return 'special';
        }

        return null;
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
