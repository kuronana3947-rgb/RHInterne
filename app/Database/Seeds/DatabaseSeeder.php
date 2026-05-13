<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Seeds\CongeSeeder;
use App\Database\Seeds\DepartementSeeder;
use App\Database\Seeds\EmployeSeeder;
use App\Database\Seeds\SoldeSeeder;
use App\Database\Seeds\StatutSeeder;
use App\Database\Seeds\TypeCongeSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            DepartementSeeder::class,
            TypeCongeSeeder::class,
            StatutSeeder::class,
            EmployeSeeder::class,
            SoldeSeeder::class,
            CongeSeeder::class,
        ] as $seeder) {
            $this->call($seeder);
        }
    }
}