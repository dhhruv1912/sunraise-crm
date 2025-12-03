<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'fname' => 'Dhruv',
                'lname' => 'Vadadoriya',
                'password' => '$2y$10$xLtnlvGVCJvQf/WFbTfsoeF8R9qoMBkIw..SBkh0y6rBtQu.UIrfi',
                'key' => null,
                'password_d' => 'Admin@123',
                'role' => 2,
                'status' => 1,
                'mobile' => '8490831566',
                'email' => 'dhruvvadadoriya1566@gmail.com',
                'activity' => 1,
                'salary' => 20000.00,
                'varify' => 1,
                'created_at' => '2024-09-14 18:36:12',
                'updated_at' => '2024-09-14 13:06:12',
            ],
            [
                'id' => 2,
                'fname' => 'CMO',
                'lname' => 'Officer',
                'password' => '$2y$10$l0B0xpY771rkbTy6xaa9C.Hebgv4F5dXhXhOAskV6rbsAIiK0JqCO',
                'key' => null,
                'password_d' => 'Admin@123',
                'role' => 3,
                'status' => 1,
                'mobile' => '9913196088',
                'email' => 'cmo@sunraise.com',
                'activity' => 0,
                'salary' => null,
                'varify' => 0,
                'created_at' => '2024-05-12 12:09:43',
                'updated_at' => '2024-05-12 12:09:43',
            ],
            [
                'id' => 3,
                'fname' => 'Marketing',
                'lname' => 'Head',
                'password' => '$2y$10$aCHAUEp3NgAGSShw86wY1uFU2f6bKikb6f7DydorcGuk.6Om6QplG',
                'key' => null,
                'password_d' => 'Admin@123',
                'role' => 4,
                'status' => 1,
                'mobile' => '8141415030',
                'email' => 'marketing@head.com',
                'activity' => 0,
                'salary' => null,
                'varify' => 0,
                'created_at' => '2024-05-12 12:11:11',
                'updated_at' => '2024-05-12 12:11:11',
            ],
            [
                'id' => 4,
                'fname' => 'General',
                'lname' => 'Administrative',
                'password' => '$2y$10$MTyOjeSV2xRSPj5WuLP4AuDHERPmZxBTcpkpkPOS9AcsGBMgb/E1e',
                'key' => null,
                'password_d' => 'Admin@123',
                'role' => 1,
                'status' => 1,
                'mobile' => '8181818181',
                'email' => 'general@administrative.com',
                'activity' => 0,
                'salary' => null,
                'varify' => 0,
                'created_at' => '2024-05-21 20:49:01',
                'updated_at' => '2024-05-21 15:19:01',
            ],
        ]);
    }
}
