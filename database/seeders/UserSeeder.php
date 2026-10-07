<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUser('Store Admin', 'admin@denim.test', 'Admin@12345', UserRole::Admin);
        $this->seedUser('Demo Customer', 'customer@denim.test', 'Customer@12345', UserRole::Customer);
    }

    private function seedUser(string $name, string $email, string $password, UserRole $role): void
    {
        // Role is not mass-assignable, so it is set explicitly here.
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = $password;
        $user->role = $role;
        $user->email_verified_at = now();
        $user->save();
    }
}
