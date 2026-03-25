<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolUsersSeeder extends Seeder
{
    /**
     * Seed school users (admin, teachers, students).
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@school.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $teacherNames = [
            'Prof Martin',
            'Prof Dubois',
            'Prof Bernard',
            'Prof Leroy',
        ];

        foreach ($teacherNames as $index => $teacherName) {
            User::query()->create([
                'name' => $teacherName,
                'email' => 'prof'.($index + 1).'@school.test',
                'password' => 'password',
                'role' => 'prof',
            ]);
        }

        for ($i = 1; $i <= 24; $i++) {
            User::query()->create([
                'name' => 'Eleve '.$i,
                'email' => 'eleve'.$i.'@school.test',
                'password' => 'password',
                'role' => 'eleve',
            ]);
        }
    }
}
