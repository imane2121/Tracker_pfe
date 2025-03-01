<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the table before seeding
        DB::table('users')->delete();

        // Seed admin user
        $admin = [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'verified' => true,
            'role' => 'admin',
            'account_status' => 'active',
            'CNI' => 'ADMIN123456',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        DB::table('users')->insert($admin);

        // Sample supervisor data with phone numbers
        $supervisors = [
            ['first_name' => 'Karim', 'last_name' => 'El Amrani', 'email' => 'karim.elamrani@example.com', 'CNI' => 'AB123456', 'city_id' => 1, 'organisation_id' => 1, 'phone_number' => '+212600000001'],
            ['first_name' => 'Fatima', 'last_name' => 'Zahra', 'email' => 'fatima.zahra@example.com', 'CNI' => 'CD654321', 'city_id' => 2, 'organisation_id' => 2, 'phone_number' => '+212600000002'],
            ['first_name' => 'Youssef', 'last_name' => 'Benali', 'email' => 'youssef.benali@example.com', 'CNI' => 'EF789012', 'city_id' => 3, 'organisation_id' => 3, 'phone_number' => '+212600000003'],
            ['first_name' => 'Layla', 'last_name' => 'Naciri', 'email' => 'layla.naciri@example.com', 'CNI' => 'GH345678', 'city_id' => 4, 'organisation_id' => 4, 'phone_number' => '+212600000004'],
            ['first_name' => 'Hassan', 'last_name' => 'Tazi', 'email' => 'hassan.tazi@example.com', 'CNI' => 'IJ901234', 'city_id' => 5, 'organisation_id' => 5, 'phone_number' => '+212600000005'],
            ['first_name' => 'Nadia', 'last_name' => 'Mekki', 'email' => 'nadia.mekki@example.com', 'CNI' => 'KL567890', 'city_id' => 6, 'organisation_id' => 1, 'phone_number' => '+212600000006'],
            ['first_name' => 'Omar', 'last_name' => 'El Haddad', 'email' => 'omar.elhaddad@example.com', 'CNI' => 'MN123456', 'city_id' => 7, 'organisation_id' => 2, 'phone_number' => '+212600000007'],
            ['first_name' => 'Salma', 'last_name' => 'Cherkaoui', 'email' => 'salma.cherkaoui@example.com', 'CNI' => 'OP789012', 'city_id' => 8, 'organisation_id' => 3, 'phone_number' => '+212600000008'],
            ['first_name' => 'Mohamed', 'last_name' => 'Bakkali', 'email' => 'mohamed.bakkali@example.com', 'CNI' => 'QR345678', 'city_id' => 9, 'organisation_id' => 4, 'phone_number' => '+212600000009'],
            ['first_name' => 'Imane', 'last_name' => 'Drissi', 'email' => 'imane.drissi@example.com', 'CNI' => 'ST901234', 'city_id' => 10, 'organisation_id' => 5, 'phone_number' => '+212600000010']
        ];
        
        foreach ($supervisors as &$supervisor) {
            $supervisor['email_verified_at'] = Carbon::now();
            $supervisor['password'] = Hash::make('password');
            $supervisor['verified'] = true;
            $supervisor['role'] = 'supervisor';
            $supervisor['account_status'] = 'active';
            $supervisor['created_at'] = Carbon::now();
            $supervisor['updated_at'] = Carbon::now();
        }
        DB::table('users')->insert($supervisors);

        // Seed contributor users
        $contributors = [];
        for ($i = 1; $i <= 30; $i++) {
            $contributors[] = [
                'first_name' => 'User' . $i,
                'last_name' => 'Contributor',
                'email' => 'user' . $i . '@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'verified' => true,
                'role' => 'contributor',
                'phone_number' => '+21260000000' . $i,
                'username' => 'user' . $i,
                'credibility_score' => rand(70, 100),
                'account_status' => 'active',
                'CNI' => strtoupper(substr(md5(uniqid()), 0, 8)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('users')->insert($contributors);
    }
}
