<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['employe_id', 'type_conge_id', 'annee', 'jours_attribues', 'jours_pris'];
    protected $useTimestamps = false;

    public function getSoldeDisponible($employeId, $typeCongeId, $annee = null)
    {
        $annee = $annee ?? date('Y');
        $solde = $this->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->first();

        if (!$solde) {
            return 0;
        }

        return $solde['jours_attribues'] - $solde['jours_pris'];
    }
}
