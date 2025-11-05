<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduleIdToVotes extends Migration
{
    public function up()
    {
        $fields = [
            'schedule_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'candidate_id',
            ],
        ];

        $this->forge->addColumn('votes', $fields);

        $this->db->query('ALTER TABLE `votes` ADD INDEX `votes_schedule_id_idx` (`schedule_id`)');

        $schedule = $this->db->table('schedules')
            ->orderBy('start_time', 'DESC')
            ->get(1)
            ->getRowArray();

        if ($schedule) {
            $this->db->table('votes')
                ->where('schedule_id', null)
                ->set('schedule_id', $schedule['id_schedule'])
                ->update();
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('schedule_id', 'votes')) {
            $this->forge->dropColumn('votes', 'schedule_id');
        }
    }
}
