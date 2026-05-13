<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SoldeSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('soldes');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'employe_id' => 1,
                'type_conge_id' => 1,
                'annee' => 2025,
                'jours_attribues' => 30,
                'jours_pris' => 12,
            ],
            [
                'id' => 2,
                'employe_id' => 1,
                'type_conge_id' => 2,
                'annee' => 2025,
                'jours_attribues' => 10,
                'jours_pris' => 2,
            ],
            [
                'id' => 3,
                'employe_id' => 1,
                'type_conge_id' => 3,
                'annee' => 2025,
                'jours_attribues' => 5,
                'jours_pris' => 4,
            ],
            [
                'id' => 4,
                'employe_id' => 4,
                'type_conge_id' => 1,
                'annee' => 2025,
                'jours_attribues' => 30,
                'jours_pris' => 8,
            ],
            [
                'id' => 5,
                'employe_id' => 4,
                'type_conge_id' => 2,
                'annee' => 2025,
                'jours_attribues' => 10,
                'jours_pris' => 1,
            ],
            [
                'id' => 6,
                'employe_id' => 5,
                'type_conge_id' => 1,
                'annee' => 2025,
                'jours_attribues' => 30,
                'jours_pris' => 14,
            ],
            [
                'id' => 7,
                'employe_id' => 5,
                'type_conge_id' => 2,
                'annee' => 2025,
                'jours_attribues' => 10,
                'jours_pris' => 0,
            ],
            [
                'id' => 8,
                'employe_id' => 6,
                'type_conge_id' => 1,
                'annee' => 2025,
                'jours_attribues' => 30,
                'jours_pris' => 6,
            ],
            [
                'id' => 9,
                'employe_id' => 6,
                'type_conge_id' => 3,
                'annee' => 2025,
                'jours_attribues' => 5,
                'jours_pris' => 1,
            ],
        ]);
    }
}