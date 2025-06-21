<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo customer test với email + phone
        Customer::firstOrCreate(
            ['email' => 'customer@thanshoes.vn'],
            [
                'name' => 'Khách hàng Test',
                'email' => 'customer@thanshoes.vn',
                'phone' => '0987654321',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'password' => Hash::make('customer123456'),
            ]
        );

        // Tạo customer chỉ có phone
        Customer::firstOrCreate(
            ['phone' => '+84-901-234-567'],
            [
                'name' => 'Khách hàng Phone',
                'phone' => '+84-901-234-567',
                'address' => '456 Đường XYZ, Quận 2, TP.HCM',
                'password' => Hash::make('phone123456'),
            ]
        );

        // Tạo customer chỉ có email
        Customer::firstOrCreate(
            ['email' => 'email.only@thanshoes.vn'],
            [
                'name' => 'Khách hàng Email',
                'email' => 'email.only@thanshoes.vn',
                'address' => '789 Đường DEF, Quận 3, TP.HCM',
                'password' => Hash::make('email123456'),
            ]
        );
    }
}
