<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEntriUsada extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_entri' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_wuku' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_saptawara' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_pancawara' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'nama_penyakit' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'gejala' => [
                'type' => 'TEXT',
            ],
            'penyebab' => [
                'type' => 'TEXT',
            ],
            'ritual' => [
                'type' => 'TEXT',
            ],
            'teks_asli' => [
                'type' => 'LONGTEXT',
            ],
            'lembar_awal' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'lembar_akhir' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id_entri', true);

        // Foreign Key
        $this->forge->addForeignKey(
            'id_wuku',
            'wuku',
            'id_wuku',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'id_saptawara',
            'saptawara',
            'id_saptawara',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'id_pancawara',
            'pancawara',
            'id_pancawara',
            'CASCADE',
            'RESTRICT'
        );

        // Membuat tabel
        $this->forge->createTable('entri_usada', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('entri_usada', true);
    }
}