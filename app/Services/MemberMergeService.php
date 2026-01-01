<?php

namespace App\Services;

use App\Models\Member;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberMergeService
{
    /**
     * Merge source member into target member, then soft-delete source.
     * All relations are transferred to target.
     */
    public function merge(Member $target, Member $source): array
    {
        $log = ['target_id' => $target->id, 'source_id' => $source->id, 'transfers' => []];

        DB::transaction(function () use ($target, $source, &$log) {
            // Transfer email if target doesn't have one
            if (empty($target->email) && !empty($source->email)) {
                $target->email = $source->email;
                $log['transfers'][] = 'email';
            }

            // Transfer password if target doesn't have one
            if (empty($target->password) && !empty($source->password)) {
                $target->password = $source->password;
                $log['transfers'][] = 'password';
            }

            // Transfer social accounts
            $socialCount = SocialAccount::where('member_id', $source->id)
                ->update(['member_id' => $target->id]);
            if ($socialCount) $log['transfers'][] = "social_accounts:{$socialCount}";

            // Transfer loans
            $loanCount = DB::table('loans')->where('member_id', $source->id)
                ->update(['member_id' => $target->id]);
            if ($loanCount) $log['transfers'][] = "loans:{$loanCount}";

            // Transfer fines
            $fineCount = DB::table('fines')->where('member_id', $source->id)
                ->update(['member_id' => $target->id]);
            if ($fineCount) $log['transfers'][] = "fines:{$fineCount}";

            // Transfer thesis submissions
            $thesisCount = DB::table('thesis_submissions')->where('member_id', $source->id)
                ->update(['member_id' => $target->id]);
            if ($thesisCount) $log['transfers'][] = "thesis:{$thesisCount}";

            // Transfer notifications
            $notifCount = DB::table('member_notifications')->where('member_id', $source->id)
                ->update(['member_id' => $target->id]);
            if ($notifCount) $log['transfers'][] = "notifications:{$notifCount}";

            $target->save();
            
            // Soft delete source (can be restored if needed)
            $source->delete();
            $log['source_deleted'] = true;
        });

        Log::info('Member merged', $log);
        return $log;
    }

    /**
     * Find potential duplicates for a member.
     */
    public function findDuplicates(Member $member): array
    {
        $duplicates = [];

        // By email
        if ($member->email) {
            $byEmail = Member::where('email', $member->email)
                ->where('id', '!=', $member->id)
                ->get();
            foreach ($byEmail as $m) {
                $duplicates[$m->id] = ['member' => $m, 'reason' => 'email'];
            }
        }

        // By NIM
        if ($member->nim_nidn) {
            $byNim = Member::where('nim_nidn', $member->nim_nidn)
                ->where('id', '!=', $member->id)
                ->get();
            foreach ($byNim as $m) {
                $duplicates[$m->id] = ['member' => $m, 'reason' => 'nim'];
            }
        }

        // By exact name
        $byName = Member::whereRaw('UPPER(REPLACE(name, ".", "")) = ?', [
            strtoupper(str_replace('.', '', $member->name))
        ])->where('id', '!=', $member->id)->get();
        
        foreach ($byName as $m) {
            if (!isset($duplicates[$m->id])) {
                $duplicates[$m->id] = ['member' => $m, 'reason' => 'name'];
            }
        }

        return array_values($duplicates);
    }
}
