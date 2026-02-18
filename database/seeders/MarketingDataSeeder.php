<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marketing_data')->insert([
            [
                'id' => 1,
                'quote_request_id' => 1,
                'quote_id' => 2,
                'status' => 2,
                'assignee' => 3,
                'note' => '<p>Founded</p><p>Deal Done</p><p><br></p><p><br></p><p><strong><em><u>UPDATED</u></em></strong></p>',
                'created_at' => '2024-06-29 14:01:17',
                'updated_at' => '2025-02-16 16:52:55',
            ],
            [
                'id' => 2,
                'quote_request_id' => 2,
                'quote_id' => 2,
                'status' => 2,
                'assignee' => 3,
                'note' => '<p>ASDGkrtujio</p><p>c</p><p>vxcb</p><p>cbnsklts</p><p>vfgf</p><p><br></p>',
                'created_at' => '2024-06-29 14:08:10',
                'updated_at' => '2025-02-16 17:00:06',
            ],
            [
                'id' => 3,
                'quote_request_id' => 3,
                'quote_id' => 4,
                'status' => 2,
                'assignee' => 4,
                'note' => '<p>AWS Is limitless truly</p>',
                'created_at' => '2024-06-29 14:18:24',
                'updated_at' => '2025-02-16 17:14:14',
            ],
            [
                'id' => 4,
                'quote_request_id' => 4,
                'quote_id' => 4,
                'status' => 2,
                'assignee' => 4,
                'note' => '<p>rtsgjkg</p><p>hjkghgmngh</p><p>kdlfb</p>',
                'created_at' => '2024-06-29 15:11:07',
                'updated_at' => '2025-02-22 10:51:56',
            ],
            [
                'id' => 5,
                'quote_request_id' => 1,
                'quote_id' => 2,
                'status' => 2,
                'assignee' => 2,
                'note' => '<p>New</p>',
                'created_at' => '2025-06-24 17:48:37',
                'updated_at' => '2025-09-10 10:01:07',
            ],
            [
                'id' => 6,
                'quote_request_id' => 7,
                'quote_id' => 2,
                'status' => 1,
                'assignee' => 2,
                'note' => '<p>dfghgjh tytuytutyu fhghgh</p>',
                'created_at' => '2025-09-10 09:57:54',
                'updated_at' => '2025-09-10 10:26:02',
            ],
            [
                'id' => 7,
                'quote_request_id' => 8,
                'quote_id' => 2,
                'status' => 0,
                'assignee' => 2,
                'note' => null,
                'created_at' => '2025-09-10 10:23:41',
                'updated_at' => '2025-09-10 10:23:41',
            ],
            [
                'id' => 8,
                'quote_request_id' => 9,
                'quote_id' => 2,
                'status' => 2,
                'assignee' => 2,
                'note' => null,
                'created_at' => '2025-09-10 10:35:14',
                'updated_at' => '2025-09-10 10:37:27',
            ],
        ]);
    }
}
