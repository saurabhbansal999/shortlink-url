<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => Role::SUPERADMIN,    'name' => 'Super Admin'],
            ['id' => Role::COMPANY_ADMIN, 'name' => 'Company Admin'],
            ['id' => Role::COMPANY_MEMBER,'name' => 'Company Member'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], ['name' => $role['name']]);
        }
    }
}
