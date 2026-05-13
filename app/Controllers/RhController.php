<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\EmployeModel;

class RhController extends BaseController
{
    protected $congeModel;
    protected $soldeModel;
    protected $employeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
        $this->soldeModel = new SoldeModel();
        $this->employeModel = new EmployeModel();
    }

    public function index()
    {
        $filter = $this->request->getGet('filter') ?? 'tous';
        $department = $this->request->getGet('department') ?? null;
        $currentUser = $this->getCurrentUser();

        // Récupérer les demandes selon les filtres
        $demandes = $this->congeModel->getDemandes($filter, $department);

        $allDemandes = $this->congeModel->getDemandes('tous', null);

        $departments = $this->employeModel->getDepartements();

        $statusCounts = [
            'tous' => count($allDemandes),
            'attente' => count(array_filter($allDemandes, static fn($d) => (int) ($d['statut_id'] ?? 0) === 1)),
            'approuvees' => count(array_filter($allDemandes, static fn($d) => (int) ($d['statut_id'] ?? 0) === 2)),
            'refusees' => count(array_filter($allDemandes, static fn($d) => (int) ($d['statut_id'] ?? 0) === 3)),
        ];

        $pendingCount = $statusCounts['attente'];

        $data = [
            'demandes' => $demandes,
            'filter' => $filter,
            'department' => $department,
            'pendingCount' => $pendingCount,
            'departments' => $departments,
            'statusCounts' => $statusCounts,
            'currentUser' => $currentUser,
        ];

        return view('rh/index', $data);
    }

    public function approuver($id)
    {
        $conge = $this->congeModel->find($id);

        if (!$conge) {
            return $this->response->setStatusCode(404)->setBody('Congé non trouvé');
        }

        // Approuver le congé
        $this->congeModel->update($id, [
            'statut_id' => 2, // approuvé
            'traite_par' => $this->getCurrentUserId(),
        ]);

        // Mettre à jour automatiquement le solde
        $this->mettreAJourSolde($conge['employe_id'], $conge['type_conge_id'], $conge['nb_jours']);

        session()->setFlashdata('success', 'Demande approuvée et solde mis à jour.');
        return redirect()->to('/rh/dashboard');
    }

    public function refuser($id)
    {
        $commentaire = $this->request->getPost('commentaire') ?? '';

        $this->congeModel->update($id, [
            'statut_id' => 3, // refusé
            'commentaire_rh' => $commentaire,
            'traite_par' => $this->getCurrentUserId(),
        ]);

        session()->setFlashdata('info', 'Demande refusée.');
        return redirect()->to('/rh/dashboard');
    }

    protected function mettreAJourSolde($employeId, $typeCongeId, $jours)
    {
        $annee = date('Y');
        $solde = $this->soldeModel->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->first();

        if ($solde) {
            $this->soldeModel->update($solde['id'], [
                'jours_pris' => $solde['jours_pris'] + $jours
            ]);
            return;
        }

        $typeConge = db_connect()->table('types_conge')
            ->select('jours_annuels')
            ->where('id', $typeCongeId)
            ->get()
            ->getRowArray();

        $joursAttribues = (int) ($typeConge['jours_annuels'] ?? 0);

        $this->soldeModel->insert([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'annee' => $annee,
            'jours_attribues' => $joursAttribues,
            'jours_pris' => $jours,
        ]);
    }

    public function soldesEmployes()
    {
        $employes = $this->employeModel->getSoldes();

        return view('rh/soldes', ['employes' => $employes]);
    }

    private function getCurrentUserId(): int
    {
        $user = session()->get('user') ?? [];

        return (int) ($user['id'] ?? 0);
    }

    private function getCurrentUser(): array
    {
        $userId = $this->getCurrentUserId();

        if ($userId <= 0) {
            return [
                'nom' => 'Responsable RH',
                'prenom' => '',
                'departement' => '',
                'initiales' => 'RH',
            ];
        }

        $user = db_connect()->table('employes e')
            ->select('e.nom, e.prenom, d.nom AS departement')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->where('e.id', $userId)
            ->get()
            ->getRowArray();

        if (!$user) {
            return [
                'nom' => 'Responsable RH',
                'prenom' => '',
                'departement' => '',
                'initiales' => 'RH',
            ];
        }

        $prenom = trim((string) ($user['prenom'] ?? ''));
        $nom = trim((string) ($user['nom'] ?? ''));

        return [
            'nom' => $nom !== '' ? $nom : 'Responsable RH',
            'prenom' => $prenom,
            'departement' => $user['departement'] ?? '',
            'initiales' => strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)) ?: 'RH',
        ];
    }
}