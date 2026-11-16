<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCalendarTables extends Migration
{
    public function up()
    {
        // 1. Tabel wuku
        $this->forge->addField([
            'id_wuku' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_wuku' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id_wuku', true);
        $this->forge->addKey('nama_wuku', false, true);
        $this->forge->createTable('wuku', true, [
            'ENGINE' => 'InnoDB',
        ]);

        // 2. Tabel saptawara
        $this->forge->addField([
            'id_saptawara' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_saptawara' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id_saptawara', true);
        $this->forge->addKey('nama_saptawara', false, true);
        $this->forge->createTable('saptawara', true, [
            'ENGINE' => 'InnoDB',
        ]);

        // 3. Tabel pancawara
        $this->forge->addField([
            'id_pancawara' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_pancawara' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id_pancawara', true);
        $this->forge->addKey('nama_pancawara', false, true);
        $this->forge->createTable('pancawara', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down()
    {
        // Hapus tabel dengan urutan terbalik
        $this->forge->dropTable('pancawara', true);
        $this->forge->dropTable('saptawara', true);
        $this->forge->dropTable('wuku', true);
    }
}
