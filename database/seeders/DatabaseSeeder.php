<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CustomEvent;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(20)->create();
        CustomEvent::factory()->count(100)->create();
        DB::table('permissions')->insert([
            ["name" => "GetAllUsers"],
            ["name" => "UpdateAllUsers"],
            ["name" => "UpdateAllEvents"],
            ["name" => "AttachEventsAllUsers"],
            ["name" => "deleteAllEvents"] 
        ]);
        DB::table("roles")->insert([
            "name" => "admin"
        ]);
        DB::table("roles_permissions")->insert([
            ['role_id' => 1, "permission_id" => 1],
            ['role_id' => 1, "permission_id" => 2],
            ['role_id' => 1, "permission_id" => 2],
            ['role_id' => 1, "permission_id" => 4],
            ['role_id' => 1, "permission_id" => 5],
        ]);
    }
}
