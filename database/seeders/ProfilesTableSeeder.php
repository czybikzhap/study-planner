<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'name' => 'Информационный менеджмент',
                'direction_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Управление проектом',
                'direction_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Профили для Инфокоммуникационных технологий
        DB::table('profiles')->insert([
            [
                'name' => 'Оптические системы и сети связи',
                'direction_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Системы радиосвязи и радиодоступа',
                'direction_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Системы мобильной связи',
                'direction_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('profiles')->insert([
            [
                'name' => 'Веб-разработка',
                'direction_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Мобильная разработка',
                'direction_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Data Science',
                'direction_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('profiles')->insert([
            [
                'name' => 'Кибербезопасность',
                'direction_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Криптография',
                'direction_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Защита информации',
                'direction_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
