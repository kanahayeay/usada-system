<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengobatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengobatan' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_entri' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'bahan_asli' => [
                'type' => 'TEXT',
            ],
            'bagian_tubuh' => [
                'type' => 'TEXT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'cara' => [
                'type' => 'TEXT',
            ],
        ]);

        $this->forge->addKey('id_pengobatan', true);

        $this->forge->addForeignKey(
            'id_entri',
            'entri_usada',
            'id_entri',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('pengobatan', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('pengobatan', true);
    }
}
