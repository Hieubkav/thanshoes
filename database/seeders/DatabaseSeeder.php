<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // tạo  1 user với đủ email,, mật khẩu, và tên
        \App\Models\User::factory()->create([
            'email' => 'tranmanhhieu@gmail.com',
            'password' => bcrypt('12345678'), // mã hóa thành chuỗi : 
            'name' => 'Tran Manh Hieu',
        ]);
    }
}
