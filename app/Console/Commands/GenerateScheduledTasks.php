<?php

namespace App\Console\Commands;

use App\Models\TaskTemplate;
use Illuminate\Console\Command;

class GenerateScheduledTasks extends Command
{
    protected $signature = 'tasks:generate';
    protected $description = 'Generate tasks from active templates based on schedule';

    public function handle(): int
    {
        $templates = TaskTemplate::where('is_active', true)->get();
        $generated = 0;

        foreach ($templates as $template) {
            if ($template->shouldGenerateToday()) {
                $task = $template->generateTask();
                if ($task) {
                    $generated++;
                    $this->info("Generated: {$task->title}");
                }
            }
        }

        $this->info("Total tasks generated: {$generated}");
        return self::SUCCESS;
    }
}
