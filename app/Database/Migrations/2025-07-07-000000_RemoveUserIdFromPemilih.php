<?php

use CodeIgniter\Database\Migration;

class RemoveUserIdFromPemilih extends Migration
{
    public function up()
    {
        $fieldsData = $this->db->getFieldData('pemilih');

        if ($fieldsData) {
            $fields = array_column($fieldsData, 'name');
            if (in_array('user_id', $fields, true)) {
                $this->forge->dropColumn('pemilih', 'user_id');
            }
        }
    }

    public function down()
    {
        $fieldsData = $this->db->getFieldData('pemilih');

        if ($fieldsData) {
            $fields = array_column($fieldsData, 'name');
            if (!in_array('user_id', $fields, true)) {
                $this->forge->addColumn('pemilih', [
                    'user_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'after'      => 'id_pemilih',
                    ],
                ]);
            }
        }
    }
}
