<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGlosarium extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_glosarium' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'istilah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'varian' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'bahasa' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'makna_indonesia' => [
                'type' => 'TEXT',
            ],
            'contoh_konteks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_glosarium', true);

        $this->forge->createTable('glosarium', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('glosarium', true);
    }
}
