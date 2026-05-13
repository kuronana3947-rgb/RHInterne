<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CongeSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('conges');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'employe_id' => 1,
                'type_conge_id' => 1,
                'date_debut' => '2025-06-16',
                'date_fin' => '2025-06-20',
                'nb_jours' => 5,
                'motif' => 'Congé annuel',
                'statut_id' => 1,
                'commentaire_rh' => null,
                'traite_par' => null,
                'created_at' => '2025-06-01 08:00:00',
            ],
            [
                'id' => 2,
                'employe_id' => 1,
                'type_conge_id' => 2,
                'date_debut' => '2025-06-02',
                'date_fin' => '2025-06-03',
                'nb_jours' => 2,
                'motif' => 'Repos médical',
                'statut_id' => 2,
                'commentaire_rh' => 'Demande validée',
                'traite_par' => 2,
                'created_at' => '2025-06-02 09:15:00',
            ],
            [
                'id' => 3,
                'employe_id' => 1,
                'type_conge_id' => 1,
                'date_debut' => '2025-05-12',
                'date_fin' => '2025-05-16',
                'nb_jours' => 5,
                'motif' => 'Vacances',
                'statut_id' => 2,
                'commentaire_rh' => 'Approuvé',
                'traite_par' => 2,
                'created_at' => '2025-05-12 10:30:00',
            ],
            [
                'id' => 4,
                'employe_id' => 1,
                'type_conge_id' => 3,
                'date_debut' => '2025-07-10',
                'date_fin' => '2025-07-10',
                'nb_jours' => 1,
                'motif' => 'Autorisation spéciale',
                'statut_id' => 3,
                'commentaire_rh' => 'Motif incomplet',
                'traite_par' => 2,
                'created_at' => '2025-07-01 11:00:00',
            ],
            [
                'id' => 5,
                'employe_id' => 4,
                'type_conge_id' => 1,
                'date_debut' => '2025-08-01',
                'date_fin' => '2025-08-05',
                'nb_jours' => 5,
                'motif' => 'Premier congé',
                'statut_id' => 1,
                'commentaire_rh' => null,
                'traite_par' => null,
                'created_at' => '2025-07-28 14:00:00',
            ],
            [
                'id' => 6,
                'employe_id' => 4,
                'type_conge_id' => 1,
                'date_debut' => '2025-09-15',
                'date_fin' => '2025-09-19',
                'nb_jours' => 5,
                'motif' => 'Voyage familial',
                'statut_id' => 1,
                'commentaire_rh' => null,
                'traite_par' => null,
                'created_at' => '2025-09-01 08:20:00',
            ],
            [
                'id' => 7,
                'employe_id' => 5,
                'type_conge_id' => 2,
                'date_debut' => '2025-07-08',
                'date_fin' => '2025-07-09',
                'nb_jours' => 2,
                'motif' => 'Repos médical',
                'statut_id' => 2,
                'commentaire_rh' => 'Validé par RH',
                'traite_par' => 2,
                'created_at' => '2025-07-08 09:10:00',
            ],
            [
                'id' => 8,
                'employe_id' => 6,
                'type_conge_id' => 1,
                'date_debut' => '2025-10-20',
                'date_fin' => '2025-10-24',
                'nb_jours' => 5,
                'motif' => 'Congé annuel',
                'statut_id' => 3,
                'commentaire_rh' => 'Refus pour période chargée',
                'traite_par' => 2,
                'created_at' => '2025-10-01 10:00:00',
            ],
            [
                'id' => 9,
                'employe_id' => 5,
                'type_conge_id' => 3,
                'date_debut' => '2025-11-03',
                'date_fin' => '2025-11-03',
                'nb_jours' => 1,
                'motif' => 'Autorisation spéciale',
                'statut_id' => 1,
                'commentaire_rh' => null,
                'traite_par' => null,
                'created_at' => '2025-10-28 11:00:00',
            ],
        ]);
    }
}