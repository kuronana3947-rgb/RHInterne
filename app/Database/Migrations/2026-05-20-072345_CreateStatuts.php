<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatuts extends Migration
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
            'libelle' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('statuts');
    }

    public function down()
    {
        $this->forge->dropTable('statuts', true);
    }
}