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
      $RoleItems = [
          [
              'name'        => 'Admin',
              'slug'        => 'admin',
              'description' => 'Admin Role',
              'level'       => (int)config('app.adminlevel')+1,
          ],
          [
              'name'        => 'User',
              'slug'        => 'user',
              'description' => 'User Role',
              'level'       => 1,
          ],
          [
              'name'        => 'Unverified',
              'slug'        => 'unverified',
              'description' => 'Unverified Role',
              'level'       => 0,
          ],
      ];

      /*
       * Add Role Items
       *
       */
     foreach ($RoleItems as $RoleItem) {
          $newRoleItem = config('roles.models.role')::firstOrNew(['slug' => $RoleItem['slug']]);
          $newRoleItem->name = $RoleItem['name'];
          $newRoleItem->description = $RoleItem['description'];
          $newRoleItem->level = $RoleItem['level'];
          $newRoleItem->save();
      }
      
      $user = User::create(['name' => 'Admin','username' => 'admin','email' => 'admin@admin.admin', 'password' => Hash::make('admin')]);
      $role = config('roles.models.role')::where('name', '=', 'Admin')->first();
      $user->attachRole($role);
      
      if(config("app.debug")){
        $user1 = User::create(['name' => 'Admin1','username' => 'admin1','email' => 'admin@admin.admin1', 'password' => Hash::make('admin1')]);
        $user2 = User::create(['name' => 'Admin2','username' => 'admin2','email' => 'admin@admin.admin2', 'password' => Hash::make('admin2')]);
        $user1->attachRole($role);
        $user2->attachRole($role);
      }
        //$this->call(RolesTableSeeder::class);
        //$this->call(UsersTableSeeder::class);
    }
}
