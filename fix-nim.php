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
        
        // Check if NIM exists in SIAKAD data (unlinked member)
        $siakad = Member::where('member_id', $nim)
            ->where('id', '!=', $m->id)
            ->first();
        
        if ($siakad && !$siakad->email) {
            echo "Fix: {$m->name} ({$m->member_id}) -> NIM: {$nim}\n";
            
            // Update current member with correct data from SIAKAD
            $m->update([
                'member_id' => $nim,
                'nim_nidn' => $nim,
                'branch_id' => $siakad->branch_id,
                'faculty_id' => $siakad->faculty_id,
                'department_id' => $siakad->department_id,
            ]);
            
            // Delete SIAKAD duplicate (data sudah di-merge)
            $siakad->delete();
            $fixed++;
        } else {
            echo "Skip: {$m->name} - No SIAKAD match for NIM {$nim}\n";
        }
    }
}

echo "\nFixed: {$fixed} members\n";
