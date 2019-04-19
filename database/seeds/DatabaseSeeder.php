<?php

use Illuminate\Database\Seeder;
use App\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $user = User::create(['name' => 'Admin','username' => 'admin','email' => 'admin@admin.admin', 'password' => Hash::make('admin')]);
      $user1 = User::create(['name' => 'Admin1','username' => 'admin1','email' => 'admin@admin.admin1', 'password' => Hash::make('admin1')]);
      $user2 = User::create(['name' => 'Admin2','username' => 'admin2','email' => 'admin@admin.admin2', 'password' => Hash::make('admin2')]);
      $role = config('roles.models.role')::where('name', '=', 'Admin')->first();
      $user->attachRole($role);
      $user1->attachRole($role);
      $user2->attachRole($role);
        // $this->call(UsersTableSeeder::class);
    }
}
