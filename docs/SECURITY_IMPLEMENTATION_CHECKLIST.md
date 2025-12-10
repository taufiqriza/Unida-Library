# 📋 IMPLEMENTASI SECURITY HARDENING CHECKLIST

## Panduan Implementasi Hasil Audit

Dokumen ini berisi langkah-langkah implementasi konkret untuk memperbaiki issue yang ditemukan dalam audit.

---

## 🔴 CRITICAL: Week 1 Actions

### 1. Enable Session Encryption
```bash
# .env
SESSION_ENCRYPT=true
```

### 2. Add Rate Limiting Middleware

**File:** `app/Http/Kernel.php` atau `bootstrap/app.php` (Laravel 12)

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleApi('60,1');
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);
    })
    ->create();
```

**Update routes/web.php:**
```php
// Add throttle to login route
Route::match(['get', 'post'], '/login', [MemberAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');
```

### 3. Create Security Headers Middleware

**File:** `app/Http/Middleware/SecurityHeaders.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
```

### 4. Create Private Thesis Storage Disk

**File:** `config/filesystems.php` - Add new disk:

```php
'disks' => [
    // ... existing disks
    
    'thesis' => [
        'driver' => 'local',
        'root' => storage_path('app/thesis'),
        'visibility' => 'private',
        'throw' => true,
    ],
],
```

### 5. Update ThesisSubmissionForm to Use Private Storage

**File:** `app/Livewire/ThesisSubmissionForm.php`

```php
protected function saveSubmission(string $status): ThesisSubmission
{
    // Change from 'public' to 'thesis' (private)
    $storageDisk = 'thesis';
    
    if ($this->cover_file) {
        $data['cover_file'] = $this->cover_file->store('covers', $storageDisk);
    }
    if ($this->approval_file) {
        $data['approval_file'] = $this->approval_file->store('approvals', $storageDisk);
    }
    if ($this->preview_file) {
        $data['preview_file'] = $this->preview_file->store('previews', $storageDisk);
    }
    if ($this->fulltext_file) {
        $data['fulltext_file'] = $this->fulltext_file->store('fulltext', $storageDisk);
    }
    // ...
}
```

### 6. Update ThesisFileController for Private Disk

**File:** `app/Http/Controllers/ThesisFileController.php`

```php
public function show(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
{
    // ... validation code ...

    // Change disk from 'public' to 'thesis'
    if (!Storage::disk('thesis')->exists($filePath)) {
        abort(404, 'File not found');
    }

    $mimeType = Storage::disk('thesis')->mimeType($filePath);
    $fileName = basename($filePath);

    return Storage::disk('thesis')->response($filePath, $fileName, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        'Content-Security-Policy' => "default-src 'none';",
        'X-Content-Type-Options' => 'nosniff',
    ]);
}

public function download(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
{
    // ... validation code ...
    
    if (!Storage::disk('thesis')->exists($filePath)) {
        abort(404, 'File not found');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $downloadName = str($submission->nim . '_' . $submission->author . '_' . $type)->slug() . '.' . $extension;

    return Storage::disk('thesis')->download($filePath, $downloadName);
}
```

---

## 🟠 HIGH PRIORITY: Week 2 Actions

### 7. Add Database Indexes Migration

**Create migration:**
```bash
php artisan make:migration add_search_indexes
```

**File:** `database/migrations/XXXX_add_search_indexes.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Books table indexes
        Schema::table('books', function (Blueprint $table) {
            $table->index('title');
            $table->index('isbn');
            $table->index('call_number');
            $table->index('publish_year');
            $table->index(['is_opac_visible', 'opac_hide']);
        });

        // Etheses table indexes
        Schema::table('etheses', function (Blueprint $table) {
            $table->index(['is_public', 'department_id']);
            $table->index('author');
            $table->index('nim');
            $table->index('year');
        });

        // Members table indexes
        Schema::table('members', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('email');
            $table->index('is_active');
        });

        // Items table indexes
        Schema::table('items', function (Blueprint $table) {
            $table->index('barcode');
            $table->index('status');
        });

        // Loans table indexes
        Schema::table('loans', function (Blueprint $table) {
            $table->index(['is_returned', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['isbn']);
            $table->dropIndex(['call_number']);
            $table->dropIndex(['publish_year']);
            $table->dropIndex(['is_opac_visible', 'opac_hide']);
        });
        // ... drop other indexes
    }
};
```

### 8. Fix Livewire Property Exposure

**File:** `app/Livewire/ThesisSubmissionForm.php`

```php
// Change from public to protected
protected ?int $memberId = null;

public function mount(?int $submissionId = null)
{
    // ... existing code ...
    
    $member = Auth::guard('member')->user();
    $this->memberId = $member?->id;
}

// Add getter if needed in view
public function getMemberIdProperty(): ?int
{
    return Auth::guard('member')->id();
}
```

### 9. Sanitize Search Input

**File:** `app/Livewire/GlobalSearch.php`

```php
public function updatingQuery($value)
{
    // Sanitize search input
    $value = strip_tags($value);
    $value = preg_replace('/[<>"\']/', '', $value);
    
    // Escape LIKE wildcards to prevent ReDoS
    $value = str_replace(['%', '_'], ['\\%', '\\_'], $value);
    
    $this->resetPage();
}

protected function searchBooks(): Collection
{
    $searchTerm = $this->query; // Already sanitized by updatingQuery
    
    // ... rest of method
}
```

### 10. Enhanced Password Policy

**File:** `app/Http/Controllers/MemberAuthController.php`

```php
use Illuminate\Validation\Rules\Password;

public function register(Request $request)
{
    if ($request->isMethod('post')) {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|max:20',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter',
            'password.letters' => 'Password harus mengandung huruf',
            'password.mixed' => 'Password harus mengandung huruf besar dan kecil',
            'password.numbers' => 'Password harus mengandung angka',
            'password.symbols' => 'Password harus mengandung simbol',
            'password.uncompromised' => 'Password terlalu umum atau pernah bocor',
        ]);
        
        // ... rest of method
    }
}
```

### 11. Improve Member ID Generation

**File:** `app/Http/Controllers/MemberAuthController.php`

```php
use Illuminate\Support\Str;

public function register(Request $request)
{
    // ... validation ...
    
    $member = Member::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'member_id' => $this->generateUniqueMemberId(),
        'register_date' => now(),
        'expire_date' => now()->addYear(),
        'is_active' => true,
    ]);
}

protected function generateUniqueMemberId(): string
{
    do {
        $id = 'M' . date('Ymd') . strtoupper(Str::random(4));
    } while (Member::where('member_id', $id)->exists());
    
    return $id;
}
```

---

## 🟡 MEDIUM PRIORITY: Week 3 Actions

### 12. Add Activity Logging

**Install spatie/laravel-activitylog:**
```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

**Add to models:**
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ThesisSubmission extends Model
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'reviewed_by', 'reviewed_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Thesis submission was {$eventName}");
    }
}
```

### 13. Implement Caching for Filter Options

**File:** `app/Livewire/GlobalSearch.php`

```php
use Illuminate\Support\Facades\Cache;

public function getBranchesProperty()
{
    return Cache::remember('branches_active', 3600, function () {
        return Branch::where('is_active', true)->orderBy('name')->get();
    });
}

public function getSubjectsProperty()
{
    return Cache::remember('subjects_all', 3600, function () {
        return Subject::orderBy('name')->limit(100)->get();
    });
}

public function getCollectionTypesProperty()
{
    return Cache::remember('collection_types', 3600, function () {
        return CollectionType::orderBy('name')->get();
    });
}

public function getFacultiesProperty()
{
    return Cache::remember('faculties_all', 3600, function () {
        return Faculty::orderBy('name')->get();
    });
}
```

### 14. Add Failed Login Logging

**File:** `app/Http/Controllers/MemberAuthController.php`

```php
use Illuminate\Support\Facades\Log;

public function login(Request $request)
{
    if ($request->isMethod('post')) {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required',
        ]);

        $member = Member::where('member_id', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if ($member && Hash::check($request->password, $member->password)) {
            // Log successful login
            Log::channel('security')->info('Member login success', [
                'member_id' => $member->member_id,
                'ip' => $request->ip(),
            ]);
            
            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard');
        }

        // Log failed login attempt
        Log::channel('security')->warning('Member login failed', [
            'identifier' => $request->identifier,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors(['identifier' => 'No. Anggota/Email atau password salah']);
    }

    return view('opac.login');
}
```

**Add security log channel - `config/logging.php`:**
```php
'channels' => [
    // ... existing channels
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'debug',
        'days' => 30,
    ],
],
```

---

## 🧪 TESTING COMMANDS

```bash
# Run after implementing changes

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Verify security headers
curl -I http://localhost:8000 | grep -E "X-|Strict|Referrer|Permissions"

# Test rate limiting
for i in {1..10}; do curl -X POST http://localhost:8000/login -d "identifier=test&password=test"; done

# Check storage structure
ls -la storage/app/thesis/
```

---

## 📊 VERIFICATION CHECKLIST

After implementing, verify:

- [ ] Session encryption is enabled (check `SESSION_ENCRYPT` in `.env`)
- [ ] Rate limiting works (test with repeated login attempts)
- [ ] Security headers are present (use browser DevTools → Network tab)
- [ ] Thesis files are not publicly accessible (try direct URL access)
- [ ] Database indexes are created (`SHOW INDEX FROM books;`)
- [ ] Password policy enforces complexity
- [ ] Failed logins are logged in `storage/logs/security.log`
- [ ] Caching reduces database queries (check query count with Laravel Debugbar)

---

**Document Version:** 1.0  
**Last Updated:** December 9, 2025
