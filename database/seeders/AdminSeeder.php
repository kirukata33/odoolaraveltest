<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@odoo.local'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@odoo.local',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('✅ Admin user siap: admin@odoo.local / admin123');
    }
}
