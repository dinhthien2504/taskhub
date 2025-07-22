<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Việc cần làm', 'order' => 1],
            ['name' => 'Phân tích/Thiết kế', 'order' => 2],
            ['name' => 'Thực hiện', 'order' => 3],
            ['name' => 'Kiểm thử', 'order' => 4],
            ['name' => 'Hoàn tất', 'order' => 5],
            ['name' => 'Hủy bỏ', 'order' => 6],
        ];

        foreach ($statuses as $status) {
            DB::table('task_statuses')->insert([
                'name' => $status['name'],
                'order' => $status['order'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}