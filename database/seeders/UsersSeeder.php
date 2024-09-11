<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'subadmin']);
        Role::create(['name' => 'user']);

        Permission::create(['name' => 'ver usuarios']);
        Permission::create(['name' => 'gestionar hoteles']);

        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['ver usuarios', 'gestionar hoteles']);

        $user = User::find(1);
        $user->assignRole('admin');
        
    }
}
