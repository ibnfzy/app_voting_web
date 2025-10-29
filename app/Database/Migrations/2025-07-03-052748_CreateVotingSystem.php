<?php

use CodeIgniter\Database\Migration;

class CreateVotingSystem extends Migration
{
    public function up()
    {
        // Users table (Panitia, BPD, Pemilih)
        $this->forge->addField([
            'id_user'     => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'role'        => [
                'type'       => 'ENUM',
                'constraint' => ['panitia', 'bpd', 'pemilih'],
                'default'    => 'pemilih',
            ],
            'created_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->createTable('users');

        // Pemilih details
        $this->forge->addField([
            'id_pemilih'     => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'nik'            => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'name'           => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'tempat_lahir'   => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'tanggal_lahir'  => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'jenis_kelamin'  => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'null'       => true,
            ],
            'alamat'         => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'rt'             => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'null'       => true,
            ],
            'rw'             => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'null'       => true,
            ],
            'kelurahan'      => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'kecamatan'      => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'kabupaten'      => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'provinsi'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'email'          => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'unique'     => true,
            ],
            'validate'       => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'registered_at'  => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id_pemilih', true);
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilih');


        // Candidates (Calon)
        $this->forge->addField([
            'id_candidate' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'          => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'visi'          => [
                'type' => 'TEXT',
            ],
            'misi'          => [
                'type' => 'TEXT',
            ],
            'photo'         => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_candidate', true);
        $this->forge->createTable('candidates');

        // Schedule (Jadwal)
        $this->forge->addField([
            'id_schedule'  => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'start_time'    => [
                'type' => 'DATETIME',
            ],
            'end_time'      => [
                'type' => 'DATETIME',
            ],
            'description'   => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_schedule', true);
        $this->forge->createTable('schedules');

        // Votes
        $this->forge->addField([
            'id_vote'       => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pemilih_id'    => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'candidate_id'  => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'panitia_id'    => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'voted_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_vote', true);
        $this->forge->addForeignKey('pemilih_id', 'pemilih', 'id_pemilih', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('candidate_id', 'candidates', 'id_candidate', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('panitia_id', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->forge->createTable('votes');
    }

    public function down()
    {
        $this->forge->dropTable('votes', true);
        $this->forge->dropTable('schedules', true);
        $this->forge->dropTable('candidates', true);
        $this->forge->dropTable('pemilih', true);
        $this->forge->dropTable('users', true);
    }
}
