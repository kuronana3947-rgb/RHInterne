<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('employes');

        if ($builder->countAllResults() > 0) {
            return;
        }

        $builder->insertBatch([
            [
                'id' => 1,
                'nom' => 'Rakoto',
                'prenom' => 'Soa',
                'email' => 'employe@techmada.mg',
                'password' => password_hash('emp123', PASSWORD_DEFAULT),
                'role' => 'employe',
                'departement_id' => 1,
                'date_embauche' => '2024-01-15',
                'actif' => 1,
            ],
            [
                'id' => 2,
                'nom' => 'Ranaivo',
                'prenom' => 'Mira',
                'email' => 'rh@techmada.mg',
                'password' => password_hash('rh123', PASSWORD_DEFAULT),
                'role' => 'rh',
                'departement_id' => 2,
                'date_embauche' => '2023-09-01',
                'actif' => 1,
            ],
            [
                'id' => 3,
                'nom' => 'Andriam',
                'prenom' => 'Tiana',
                'email' => 'admin@techmada.mg',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'departement_id' => 3,
                'date_embauche' => '2022-05-10',
                'actif' => 1,
            ],
        ]);
    }
}