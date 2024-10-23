<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::create([
      'name' => 'Admin User',
      'email' => 'admin@user.ly',
      'password' => Hash::make('abc123'),
      'role_id' => 1
    ]);

    User::factory(10)->create([
      'role_id' => 2
    ]);
  }
}