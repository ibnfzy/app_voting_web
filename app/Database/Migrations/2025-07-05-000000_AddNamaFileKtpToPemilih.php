<?php

use CodeIgniter\Database\Migration;

class AddNamaFileKtpToPemilih extends Migration
{
    public function up()
    {
        $fields = [
            'nama_file_ktp' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'email',
            ],
        ];

        $this->forge->addColumn('pemilih', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pemilih', 'nama_file_ktp');
    }
}
