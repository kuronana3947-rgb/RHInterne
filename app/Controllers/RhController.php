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

        // Récupérer les demandes selon les filtres
        $demandes = $this->congeModel->getDemandes($filter, $department);

        $data = [
            'demandes' => $demandes,
            'filter' => $filter,
            'department' => $department,
            'pendingCount' => $this->congeModel->getPendingCount(),
            'departments' => $this->employeModel->getDepartements()
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
            'traite_par' => session()->get('employe_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Mettre à jour automatiquement le solde
        $this->mettreAJourSolde($conge['employe_id'], $conge['type_conge_id'], $conge['nb_jours']);

        session()->setFlashdata('success', 'Demande approuvée et solde mis à jour.');
        return redirect()->to('/rh');
    }

    public function refuser($id)
    {
        $commentaire = $this->request->getPost('commentaire') ?? '';

        $this->congeModel->update($id, [
            'statut_id' => 3, // refusé
            'commentaire_rh' => $commentaire,
            'traite_par' => session()->get('employe_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('info', 'Demande refusée.');
        return redirect()->to('/rh');
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
        }
    }

    public function soldesEmployes()
    {
        $employes = $this->employeModel->getSoldes();

        return view('rh/soldes', ['employes' => $employes]);
    }
}