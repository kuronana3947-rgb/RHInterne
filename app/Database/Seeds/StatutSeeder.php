<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StatutSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('statuts');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'libelle' => 'en_attente',
            ],
            [
                'id' => 2,
                'libelle' => 'approuve',
            ],
            [
                'id' => 3,
                'libelle' => 'refuse',
            ],
            [
                'id' => 4,
                'libelle' => 'annule',
            ],
        ]);
    }
}