<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('organisations')->delete();

        $organisations = [
            ['name' => 'WWF (World Wide Fund for Nature)', 'description' => 'Protection de l’environnement marin.', 'website' => 'https://www.worldwildlife.org'],
            ['name' => 'Greenpeace', 'description' => 'Sensibilisation et campagnes de nettoyage des océans.', 'website' => 'https://www.greenpeace.org'],
            ['name' => 'The Ocean Cleanup', 'description' => 'Spécialisé dans le nettoyage des déchets marins.', 'website' => 'https://theoceancleanup.com'],
            ['name' => 'Associations locales pour la protection des plages', 'description' => 'Organisations locales dédiées à la protection de l’environnement côtier.', 'website' => null],
            ['name' => 'Programme des Nations Unies pour l’Environnement (PNUE)', 'description' => 'Organisation internationale dédiée à la protection de l’environnement.', 'website' => 'https://www.unep.org'],
            ['name' => 'Départements de biologie marine', 'description' => 'Facultés et instituts étudiant la biologie marine et l’impact environnemental.', 'website' => null],
            ['name' => 'Facultés des sciences environnementales et biologiques', 'description' => 'Universités spécialisées dans les sciences environnementales.', 'website' => null],
            ['name' => 'Sociétés spécialisées dans le recyclage des déchets', 'description' => 'Entreprises privées axées sur le recyclage et la gestion des déchets.', 'website' => null],
            ['name' => 'Étudiants en biologie', 'description' => 'Étudier l’impact des déchets sur la faune et la flore marines.', 'website' => null],
            ['name' => 'Étudiants en ingénierie environnementale', 'description' => 'Proposer des solutions de gestion et de recyclage des déchets.', 'website' => null],
            ['name' => 'Étudiants en génie civil', 'description' => 'Planifier et organiser les campagnes de nettoyage.', 'website' => null],
        ];

        foreach ($organisations as &$organisation) {
            $organisation['created_at'] = Carbon::now();
            $organisation['updated_at'] = Carbon::now();
        }

        DB::table('organisations')->insert($organisations);
    }
}
