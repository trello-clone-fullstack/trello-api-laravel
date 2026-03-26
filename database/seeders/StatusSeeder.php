<?php

namespace Database\Seeders;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['À faire', '#ff0000'],
            ['En cours', '#d69d22'],
            ['Terminé', '#84df69']
        ];

        foreach ($statuses as [$name, $color]) {
            Status::create([
                'status_name' => $name,
                'color' => $color,
                'user_id' => 1
            ]);
        }
    }
}
