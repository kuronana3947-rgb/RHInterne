<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypeCongeSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('types_conge');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'libelle' => 'Congé annuel',
                'jours_annuels' => 30,
                'deductible' => 1,
            ],
            [
                'id' => 2,
                'libelle' => 'Congé maladie',
                'jours_annuels' => 10,
                'deductible' => 1,
            ],
            [
                'id' => 3,
                'libelle' => 'Congé spécial',
                'jours_annuels' => 5,
                'deductible' => 1,
            ],
        ]);
    }
}