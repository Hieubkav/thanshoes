<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user mặc định
        User::firstOrCreate(
            ['email' => 'admin@thanshoes.vn'],
            [
                'name' => 'Admin ThanShoes',
                'email' => 'admin@thanshoes.vn',
                'phone' => '0901234567',
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
            ]
        );

        // Tạo user thường để test
        User::firstOrCreate(
            ['email' => 'user@thanshoes.vn'],
            [
                'name' => 'User Test',
                'email' => 'user@thanshoes.vn',
                'phone' => '0987654321',
                'password' => Hash::make('user123456'),
                'email_verified_at' => now(),
            ]
        );

        // Tạo user chỉ có phone
        User::firstOrCreate(
            ['phone' => '+84-901-234-567'],
            [
                'name' => 'User Phone Only',
                'phone' => '+84-901-234-567',
                'password' => Hash::make('phone123456'),
                'email_verified_at' => now(),
            ]
        );
    }
}
