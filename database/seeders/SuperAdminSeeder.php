<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->upsert([
            [
                'ulid'              => (string) Str::ulid(),
                'role_id'           => Role::SUPERADMIN,
                'name'              => 'John Doe',
                'email'             => 'superadmin@yopmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ], ['email'], ['name', 'role_id', 'updated_at']);
    }
}
