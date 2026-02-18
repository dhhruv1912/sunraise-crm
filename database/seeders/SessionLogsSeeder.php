<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionLogsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('session_logs')->insert([
            ['id'=>1,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 08:16:11 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 08:16:11','updated_at'=>'2024-09-07 08:16:11'],
            ['id'=>2,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 09:26:14 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 09:26:14','updated_at'=>'2024-09-07 09:26:14'],
            ['id'=>3,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 09:26:16 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 09:26:16','updated_at'=>'2024-09-07 09:26:16'],
            ['id'=>4,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 09:26:19 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 09:26:19','updated_at'=>'2024-09-07 09:26:19'],
            ['id'=>5,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 09:26:23 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 09:26:23','updated_at'=>'2024-09-07 09:26:23'],
            ['id'=>6,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 10:14:44 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 10:14:44','updated_at'=>'2024-09-07 10:14:44'],
            ['id'=>7,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 11:31:20 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 11:31:20','updated_at'=>'2024-09-07 11:31:20'],
            ['id'=>8,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 11:35:55 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 11:35:55','updated_at'=>'2024-09-07 11:35:55'],
            ['id'=>9,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 12:20:27 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:20:27','updated_at'=>'2024-09-07 12:20:27'],
            ['id'=>10,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 12:20:35 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:20:35','updated_at'=>'2024-09-07 12:20:35'],
            ['id'=>11,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 12:20:36 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:20:36','updated_at'=>'2024-09-07 12:20:36'],
            ['id'=>12,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 12:42:51 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:42:51','updated_at'=>'2024-09-07 12:42:51'],
            ['id'=>13,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 12:43:17 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:43:17','updated_at'=>'2024-09-07 12:43:17'],
            ['id'=>14,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 12:43:25 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:43:25','updated_at'=>'2024-09-07 12:43:25'],
            ['id'=>15,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 12:43:29 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:43:29','updated_at'=>'2024-09-07 12:43:29'],
            ['id'=>16,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Ended at 12:55:24 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 12:55:24','updated_at'=>'2024-09-07 12:55:24'],
            ['id'=>17,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'Started at 15:07:16 07-09-2024','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.69.116','created_at'=>'2024-09-07 15:07:16','updated_at'=>'2024-09-07 15:07:16'],
            ['id'=>18,'staffId'=>1,'location'=>null,'message'=>'Ended at 08:13:10 14-09-2024','lendmark'=>null,'device'=>'Windows on Desktop','ip'=>null,'created_at'=>'2024-09-14 08:13:10','updated_at'=>'2024-09-14 08:13:10'],
            ['id'=>19,'staffId'=>1,'location'=>null,'message'=>'Started at 08:13:12 14-09-2024','lendmark'=>null,'device'=>'Windows on Desktop','ip'=>null,'created_at'=>'2024-09-14 08:13:12','updated_at'=>'2024-09-14 08:13:12'],
            ['id'=>20,'staffId'=>1,'location'=>null,'message'=>'Ended at 08:15:20 14-09-2024','lendmark'=>null,'device'=>'Windows on Desktop','ip'=>null,'created_at'=>'2024-09-14 08:15:20','updated_at'=>'2024-09-14 08:15:20'],
            ['id'=>21,'staffId'=>1,'location'=>null,'message'=>'Started at 08:15:29 14-09-2024','lendmark'=>null,'device'=>'Windows on Desktop','ip'=>null,'created_at'=>'2024-09-14 08:15:29','updated_at'=>'2024-09-14 08:15:29'],
            ['id'=>22,'staffId'=>1,'location'=>null,'message'=>'User Dhruv Vadadoriya Logged Out at 11:13:38 14-09-2024','lendmark'=>null,'device'=>'Windows on Desktop','ip'=>null,'created_at'=>'2024-09-14 11:13:38','updated_at'=>'2024-09-14 11:13:38'],
            ['id'=>23,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'User Dhruv Vadadoriya Logged in at 17:45:11 24-06-2025','lendmark'=>'Ahmedabad, Gujarat','device'=>'Windows on Desktop','ip'=>'43.228.96.19','created_at'=>'2025-06-24 17:45:11','updated_at'=>'2025-06-24 17:45:11'],
            ['id'=>24,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 06:47:19 08-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.81.115','created_at'=>'2025-07-08 06:47:19','updated_at'=>'2025-07-08 06:47:19'],
            ['id'=>25,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 05:32:29 10-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.81.131','created_at'=>'2025-07-10 05:32:29','updated_at'=>'2025-07-10 05:32:29'],
            ['id'=>26,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 11:36:23 16-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.81.131','created_at'=>'2025-07-16 11:36:23','updated_at'=>'2025-07-16 11:36:23'],
            ['id'=>27,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 08:13:21 18-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.81.131','created_at'=>'2025-07-18 08:13:21','updated_at'=>'2025-07-18 08:13:21'],
            ['id'=>28,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 11:36:10 19-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.81.63','created_at'=>'2025-07-19 11:36:10','updated_at'=>'2025-07-19 11:36:10'],
            ['id'=>29,'staffId'=>1,'location'=>'22.2994,73.2081','message'=>'User Dhruv Vadadoriya Logged in at 18:51:39 20-07-2025','lendmark'=>'Vadodara, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.81.63','created_at'=>'2025-07-20 18:51:39','updated_at'=>'2025-07-20 18:51:39'],
            ['id'=>30,'staffId'=>1,'location'=>'21.1959,72.8302','message'=>'User Dhruv Vadadoriya Logged in at 12:48:31 30-08-2025','lendmark'=>'Surat, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.83.147','created_at'=>'2025-08-30 12:48:31','updated_at'=>'2025-08-30 12:48:31'],
            ['id'=>31,'staffId'=>1,'location'=>'21.1959,72.8302','message'=>'User Dhruv Vadadoriya Logged in at 13:12:40 30-08-2025','lendmark'=>'Surat, Gujarat','device'=>'Windows on Desktop','ip'=>'49.36.83.147','created_at'=>'2025-08-30 13:12:40','updated_at'=>'2025-08-30 13:12:40'],
            ['id'=>32,'staffId'=>1,'location'=>'21.1959,72.8302','message'=>'User Dhruv Vadadoriya Logged in at 13:16:15 30-08-2025','lendmark'=>'Surat, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.83.147','created_at'=>'2025-08-30 13:16:15','updated_at'=>'2025-08-30 13:16:15'],
            ['id'=>33,'staffId'=>1,'location'=>'21.1959,72.8302','message'=>'User Dhruv Vadadoriya Logged in at 09:52:43 10-09-2025','lendmark'=>'Surat, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.83.27','created_at'=>'2025-09-10 09:52:43','updated_at'=>'2025-09-10 09:52:43'],
            ['id'=>34,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'User Dhruv Vadadoriya Logged in at 16:52:45 19-11-2025','lendmark'=>'Ahmedabad, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.71.219','created_at'=>'2025-11-19 16:52:45','updated_at'=>'2025-11-19 16:52:45'],
            ['id'=>35,'staffId'=>1,'location'=>'23.0258,72.5873','message'=>'User Dhruv Vadadoriya Logged in at 09:28:32 20-11-2025','lendmark'=>'Ahmedabad, Gujarat','device'=>'macOS on Desktop','ip'=>'49.36.71.219','created_at'=>'2025-11-20 09:28:32','updated_at'=>'2025-11-20 09:28:32'],
        ]);
    }
}
