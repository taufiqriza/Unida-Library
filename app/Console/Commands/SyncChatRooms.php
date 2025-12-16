<?php

namespace App\Console\Commands;

use App\Services\ChatService;
use Illuminate\Console\Command;

class SyncChatRooms extends Command
{
    protected $signature = 'chat:sync-rooms';
    protected $description = 'Sync auto chat groups (global, branch) with current staff members';

    public function handle(ChatService $chatService): int
    {
        $this->info('🔄 Syncing Chat Rooms...');
        $this->newLine();

        // Sync global room
        $this->info('📢 Syncing Global Room (Semua Staff)...');
        $chatService->syncGlobalRoomMembers();
        $globalRoom = $chatService->getGlobalRoom();
        $this->line("   ✓ Members: {$globalRoom->members()->count()}");

        $this->newLine();

        // Sync branch rooms
        $this->info('🏢 Syncing Branch Rooms...');
        $branches = \App\Models\Branch::all();
        
        foreach ($branches as $branch) {
            $chatService->syncBranchRoomMembers($branch);
            $branchRoom = $chatService->getBranchRoom($branch);
            $memberCount = $branchRoom->members()->count();
            $this->line("   ✓ {$branch->name}: {$memberCount} members");
        }

        $this->newLine();
        $this->info('✅ Chat rooms synced successfully!');

        return Command::SUCCESS;
    }
}
