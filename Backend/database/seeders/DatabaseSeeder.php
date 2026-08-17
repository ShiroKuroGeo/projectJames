<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'info.alfeser.shiro@gmail.com',
            'password' => Hash::make('ShiroP@ssword'),
            'role' => 'super_admin',
            'image' => 'venue_images/logo.jpg',
            'email_verified_at' => now()
        ]);
    }
}
