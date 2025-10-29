<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Users extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insertBatch([
            [
                'username' => 'panitia',
                'password' => password_hash('123', PASSWORD_BCRYPT),
                'role' => 'panitia',
            ],
            [
                'username' => 'bpd',
                'password' => password_hash('123', PASSWORD_BCRYPT),
                'role' => 'bpd',
            ]
        ]);

        $faker = \Faker\Factory::create('id_ID');

        for ($i = 1; $i <= 15; $i++) {
            $pemilihData = [
                'nik'           => $faker->unique()->nik(),
                'name'          => $faker->name(),
                'tempat_lahir'  => $faker->city,
                'tanggal_lahir' => $faker->date(),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'alamat'        => $faker->address,
                'rt'            => str_pad($faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                'rw'            => str_pad($faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                'kelurahan'     => $faker->streetName,
                'kecamatan'     => $faker->city,
                'kabupaten'     => $faker->city,
                'provinsi'      => $faker->state,
                'email'         => 'pemilih' . $i . '@example.com',
                'registered_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->table('pemilih')->insert($pemilihData);
        }
    }
}
