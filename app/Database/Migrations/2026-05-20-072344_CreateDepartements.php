<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartements extends Migration
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
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('departements');
    }

    public function down()
    {
        $this->forge->dropTable('departements', true);
    }
}
