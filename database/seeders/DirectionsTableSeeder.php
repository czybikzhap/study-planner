<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('directions')->insert([
            [
                'name' => '«Менеджмент»',
                'number' => '080200.62',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '«Инфокоммуникационные технологии и системы связи»',
                'number' => '210700.62',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '«Программная инженерия»',
                'number' => '230100.62',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '«Информационная безопасность»',
                'number' => '090900.62',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
