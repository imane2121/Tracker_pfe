<?php
namespace Database\Seeders; 
use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::create(['name' => 'Casablanca', 'region' => 'Casablanca-Settat']);
        City::create(['name' => 'Rabat', 'region' => 'Rabat-Salé-Kénitra']);
        City::create(['name' => 'Marrakech', 'region' => 'Marrakech-Safi']);
    }
}