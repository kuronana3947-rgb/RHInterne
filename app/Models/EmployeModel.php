<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nom', 'prenom', 'email', 'password', 'role', 'departement_id', 'date_embauche', 'actif'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'nom' => 'required|min_length[3]',
        'prenom' => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[employes.email]',
        'password' => 'required|min_length[6]',
        'role' => 'required',
        'departement_id' => 'required',
        'date_embauche' => 'required|valid_date',
        'actif' => 'required'
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom est obligatoire.',
            'min_length' => 'Le nom dois comporter au moins 3 caractères.'
        ],
        'prenom' => [
            'required' => "Le prénom est obligatoire."
        ],
        'email' => [
            'required' => "L'email est obligatoire.",
            'valid_email' => "L'email doit être une adresse email valide.",
            'is_unique' => "Cet email est déjà utilisé."
        ],
        'password' => [
            'required' => "Le mot de passe est obligatoire.",
            'min_length' => "Le mot de passe doit comporter au moins 6 caractères."
        ],
        'role' => [
            'required' => "Le rôle est obligatoire."
        ],
        'departement_id' => [
            'required' => "Le département est obligatoire."
        ],
        'date_embauche' => [
            'required' => "La date d'embauche est obligatoire.",
            'valid_date' => "La date d'embauche doit être une date valide."
        ],
        'actif' => [
            'required' => "L'état d'activité est obligatoire."
        ]
    ];

    public function getDepartements()
    {
        $db = \Config\Database::connect();
        return $db->table('departements')->select('nom')->distinct()->findAll();
    }

    public function getSoldes()
    {
        return $this->select('employes.*, soldes.jours_attribues, soldes.jours_pris, types_conge.libelle')
            ->join('soldes', 'soldes.employe_id = employes.id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.annee', date('Y'))
            ->findAll();
    }
}