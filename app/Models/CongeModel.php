<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['employe_id', 'type_conge_id', 'date_debut', 'date_fin', 'nb_jours', 'motif', 'statut_id', 'commentaire_rh', 'traite_par', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDemandes($filter = 'tous', $department = null)
    {
        $this->select('conges.*, employes.nom, employes.prenom, employes.id as employe_id, departements.nom as dept_nom, types_conge.libelle as type_libelle, statuts.libelle as statut_libelle')
            ->join('employes', 'employes.id = conges.employe_id')
            ->join('departements', 'departements.id = employes.departement_id')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('statuts', 'statuts.id = conges.statut_id');

        if ($filter === 'attente') {
            $this->where('conges.statut_id', 1);
        } elseif ($filter === 'approuvees') {
            $this->where('conges.statut_id', 2);
        } elseif ($filter === 'refusees') {
            $this->where('conges.statut_id', 3);
        }

        if ($department) {
            $this->where('departements.nom', $department);
        }

        return $this->orderBy('conges.created_at', 'DESC')->findAll();
    }

    public function getPendingCount()
    {
        return $this->where('statut_id', 1)->countAllResults();
    }
}
