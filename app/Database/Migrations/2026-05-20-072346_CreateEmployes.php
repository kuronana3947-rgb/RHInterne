<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'prenom' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'departement_id' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
            'date_embauche' => [
                'type' => 'DATE',
            ],
            'actif' => [
                'type' => 'INTEGER',
                'constraint' => 11,
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('departement_id', 'departements', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('employes');
    }

    public function down()
    {
        $this->forge->dropTable('employes', true);
    }
}