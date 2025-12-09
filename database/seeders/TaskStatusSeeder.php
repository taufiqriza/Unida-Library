<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (TaskStatus::getDefaultStatuses() as $status) {
            TaskStatus::firstOrCreate(
                ['slug' => $status['slug'], 'project_id' => null],
                $status
            );
        }
    }
}
