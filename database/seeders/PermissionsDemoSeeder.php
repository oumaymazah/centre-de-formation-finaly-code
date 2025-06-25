<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $superAdmin= Role::create(['name'=>'super-admin']);
        $admin = Role::create(['name'=>'admin']);
        $professeur= Role::create(['name'=>'professeur']);
        $etudiant= Role::create(['name'=>'etudiant']);
        \App\Models\User::factory()->create([
            'name' => 'oumayma',
            'lastname' => 'zahrouni',
            'phone'=>'90120430',
            'email' => 'els.center2022@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('superAdmin123'),
            'status' =>'active'
        ])->assignRole($superAdmin);


    }
}
