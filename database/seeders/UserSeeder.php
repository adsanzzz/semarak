<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'User Toko',
            'email' => 'toko@example.com',
            'password' => Hash::make('password'),
            'role' => 2, // user_toko
        ]);

        User::create([
            'name' => 'User Buyer',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'role' => 3, // user_buyer
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1, // admin
        ]);
    }
}
