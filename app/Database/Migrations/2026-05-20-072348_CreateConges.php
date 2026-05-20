<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConges extends Migration
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
            'employe_id' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'type_conge_id' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'date_debut' => [
                'type' => 'DATE',
            ],
            'date_fin' => [
                'type' => 'DATE',
            ],
            'nb_jours' => [
                'type' => 'INTEGER',
                'constraint' => 11,
            ],
            'motif' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'statut_id' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'commentaire_rh' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'traite_par' => [
                'type' => 'INTEGER',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'types_conge', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('statut_id', 'statuts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('traite_par', 'employes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('conges');
    }

    public function down()
    {
        $this->forge->dropTable('conges', true);
    }
}