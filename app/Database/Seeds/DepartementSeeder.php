<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('departements');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'nom' => 'Informatique',
                'description' => 'Equipe infrastructure et applications',
            ],
            [
                'id' => 2,
                'nom' => 'Ressources Humaines',
                'description' => 'Gestion du personnel et des congés',
            ],
            [
                'id' => 3,
                'nom' => 'Comptabilite',
                'description' => 'Suivi financier et paie',
            ],
        ]);
    }
}