<?php

namespace App\Livewire\Opac;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseAccess extends Component
{
    public $revealedCredentials = [];
    public $databases = [];

    public function mount()
    {
        // Define consortium databases
        $this->databases = [
            'gale_teknik' => [
                'name' => 'Gale Academic OneFile (Teknik & Sains)',
                'provider' => 'Gale / Cengage',
                'consortium' => 'FPPTI Jawa Timur',
                'url' => 'https://link.gale.com/apps/SPJ.SP01?u=idfpptij',
                'username' => 'UnivKanB',
                'password' => 'FPPTIjatim@1',
                'collections' => '17,000+',
                'type' => 'Journals & Articles',
                'subjects' => ['Engineering', 'Computer Science', 'Physics', 'Chemistry', 'Mathematics', 'Technology'],
                'icon' => 'fa-cogs',
                'color' => 'orange',
                'description' => 'Jurnal internasional bidang teknik, sains, dan teknologi dari penerbit terkemuka.',
            ],
            'gale_humaniora' => [
                'name' => 'Gale Academic OneFile (Humaniora)',
                'provider' => 'Gale / Cengage',
                'consortium' => 'FPPTI Jawa Timur',
                'url' => 'https://link.gale.com/apps/SPJ.SP02?u=fpptijwt',
                'username' => 'UnivKanB',
                'password' => 'FPPTIjatim@1',
                'collections' => '15,000+',
                'type' => 'Journals & Articles',
                'subjects' => ['Social Sciences', 'Humanities', 'Education', 'Psychology', 'History', 'Literature'],
                'icon' => 'fa-users',
                'color' => 'purple',
                'description' => 'Jurnal internasional bidang humaniora, pendidikan, dan ilmu sosial.',
            ],
            'proquest' => [
                'name' => 'ProQuest Central',
                'provider' => 'ProQuest / Clarivate',
                'consortium' => 'FPPTI Jawa Timur',
                'url' => 'https://www.proquest.com/login',
                'username' => 'UDarussalam',
                'password' => 'FPPTIjatim@1',
                'collections' => '90,000+',
                'type' => 'Journals, Dissertations, Theses',
                'subjects' => ['Business', 'Economics', 'Health Sciences', 'Social Sciences', 'Dissertations', 'Theses'],
                'icon' => 'fa-journal-whills',
                'color' => 'blue',
                'description' => 'Database jurnal akademik terbesar dengan koleksi disertasi dan tesis internasional.',
            ],
        ];
    }

    public function revealCredential($databaseKey)
    {
        // Check if user is logged in
        if (!Auth::guard('member')->check()) {
            session()->flash('error', 'Silakan login terlebih dahulu untuk melihat kredensial.');
            return redirect()->route('login');
        }

        // Toggle reveal
        if (in_array($databaseKey, $this->revealedCredentials)) {
            $this->revealedCredentials = array_diff($this->revealedCredentials, [$databaseKey]);
        } else {
            $this->revealedCredentials[] = $databaseKey;
            
            // Log access
            $this->logAccess($databaseKey, 'reveal_credential');
        }
    }

    public function accessDatabase($databaseKey)
    {
        if (!Auth::guard('member')->check()) {
            session()->flash('error', 'Silakan login terlebih dahulu untuk mengakses database.');
            return redirect()->route('login');
        }

        // Log access
        $this->logAccess($databaseKey, 'access_database');

        // Redirect to database URL
        $url = $this->databases[$databaseKey]['url'] ?? '#';
        return redirect()->away($url);
    }

    protected function logAccess($databaseKey, $action)
    {
        try {
            DB::table('database_access_logs')->insert([
                'member_id' => Auth::guard('member')->id(),
                'database_key' => $databaseKey,
                'database_name' => $this->databases[$databaseKey]['name'] ?? $databaseKey,
                'action' => $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Table might not exist yet, log to file instead
            Log::info('Database Access', [
                'member_id' => Auth::guard('member')->id(),
                'database' => $databaseKey,
                'action' => $action,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.opac.database-access')
            ->layout('components.opac.layout', ['title' => 'Akses Database Konsorsium']);
    }
}
