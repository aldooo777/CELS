<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

/** Mengisi daftar PTN dari database/data/ptn.php */
class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = require database_path('data/ptn.php');

        foreach ($rows as [$name, $short, $city, $province]) {
            Campus::updateOrCreate(['name' => $name], [
                'short_name' => $short,
                'city'       => $city,
                'province'   => $province,
                'type'       => 'PTN',
                'is_listed'  => true,
            ]);
        }
    }
}
