<?php
// Fix members with NIM in email but auto-generated member_id

use App\Models\Member;

$members = Member::where('member_id', 'like', 'M20%')
    ->where('profile_completed', 1)
    ->whereNotNull('email')
    ->get();

$fixed = 0;
foreach ($members as $m) {
    // Check if email starts with NIM (12-15 digits)
    if (preg_match('/^(\d{12,15})@/', $m->email, $match)) {
        $nim = $match[1];
        
        // Check if NIM already exists
        $existing = Member::where('member_id', $nim)->where('id', '!=', $m->id)->first();
        
        if ($existing) {
            if (!$existing->email && !$existing->profile_completed) {
                // SIAKAD data - merge and delete
                echo "Fix: {$m->name} ({$m->member_id}) -> NIM: {$nim} (merge SIAKAD id:{$existing->id})\n";
                $m->update([
                    'member_id' => $nim,
                    'nim_nidn' => $nim,
                    'branch_id' => $existing->branch_id ?? $m->branch_id,
                    'faculty_id' => $existing->faculty_id ?? $m->faculty_id,
                    'department_id' => $existing->department_id ?? $m->department_id,
                ]);
                $existing->delete();
                $fixed++;
            } else {
                echo "Skip: {$m->name} - NIM {$nim} already linked to another member\n";
            }
        } else {
            // NIM not in database, just update
            echo "Fix: {$m->name} ({$m->member_id}) -> NIM: {$nim} (no SIAKAD)\n";
            $m->update([
                'member_id' => $nim,
                'nim_nidn' => $nim,
            ]);
            $fixed++;
        }
    }
}

echo "\nFixed: {$fixed} members\n";
