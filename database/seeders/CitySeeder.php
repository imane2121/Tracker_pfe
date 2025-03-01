<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear the table before seeding
   

        // Define cities grouped by region
        $citiesByRegion = [
            'Tanger-Tétouan-Al Hoceïma' => [
                'Tanger', 'Tétouan', 'Al Hoceïma', 'Chefchaouen', 'Larache', 'Ksar El Kebir', 'Fnideq', 'Mdiq', 'Martil', 'Ouezzane',
            ],
            'Oriental' => [
                'Oujda', 'Nador', 'Berkane', 'Taourirt', 'Jerada', 'Figuig', 'Driouch', 'Guercif', 'Al Aroui', 'Beni Ensar',
            ],
            'Fès-Meknès' => [
                'Fès', 'Meknès', 'Taza', 'Sefrou', 'Moulay Yacoub', 'El Hajeb', 'Ifrane', 'Boulemane', 'Guigou', 'Imouzzer Kandar',
            ],
            'Rabat-Salé-Kénitra' => [
                'Rabat', 'Salé', 'Kénitra', 'Skhirat', 'Témara', 'Sidi Slimane', 'Sidi Kacem', 'Khémisset', 'Ain Aouda', 'Tiflet',
            ],
            'Béni Mellal-Khénifra' => [
                'Béni Mellal', 'Khénifra', 'Fquih Ben Salah', 'Kasba Tadla', 'Azilal', 'Ouaouizeght', 'El Ksiba', 'Zaouiat Cheikh', 'Afourer', 'Aghbala',
            ],
            'Casablanca-Settat' => [
                'Casablanca', 'Settat', 'Mohammedia', 'El Jadida', 'Benslimane', 'Berrechid', 'Nouaceur', 'Sidi Bennour', 'Oulad Abbou', 'Bouznika',
            ],
            'Marrakech-Safi' => [
                'Marrakech', 'Safi', 'Essaouira', 'Youssoufia', 'Chichaoua', 'El Kelaa des Sraghna', 'Rehamna', 'Al Haouz', 'Sidi Rahhal', 'Amizmiz',
            ],
            'Drâa-Tafilalet' => [
                'Errachidia', 'Ouarzazate', 'Midelt', 'Tinghir', 'Zagora', 'Rissani', 'Goulmima', 'Alnif', 'Boumalne Dades', 'Kelaat M\'Gouna',
            ],
            'Souss-Massa' => [
                'Agadir', 'Inezgane', 'Taroudant', 'Tiznit', 'Oulad Teima', 'Tata', 'Biougra', 'Ait Melloul', 'Chtouka Ait Baha', 'Sidi Bibi',
            ],
            'Guelmim-Oued Noun' => [
                'Guelmim', 'Sidi Ifni', 'Tan-Tan', 'Assa', 'Foum Zguid', 'Bouizakarne', 'Taghjijt', 'Tighmi', 'Ait Boufoulen', 'Lakhsas',
            ],
            'Laâyoune-Sakia El Hamra' => [
                'Laâyoune', 'Boujdour', 'Tarfaya', 'Foum El Oued', 'El Marsa', 'Daoura', 'Tichla', 'Bir Gandouz', 'Jdiriya', 'Labouirat',
            ],
            'Dakhla-Oued Ed-Dahab' => [
                'Dakhla', 'Aousserd', 'Bir Anzarane', 'Gleibat El Foula', 'El Argoub', 'Labouirda', 'Oum Dreyga', 'Tichla', 'Boujdour', 'Bir Gandouz',
            ],
        ];

        // Insert cities into the database
        foreach ($citiesByRegion as $region => $cities) {
            foreach ($cities as $city) {
                City::firstOrCreate([
                    'name' => $city,
                ], [
                    'region' => $region,
                ]);
            }
        }
    }
}