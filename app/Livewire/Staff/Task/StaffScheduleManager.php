<?php

namespace App\Livewire\Staff\Task;

use App\Models\StaffSchedule;
use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;
use Livewire\Component;

class StaffScheduleManager extends Component
{
    // View mode
    public string $viewMode = 'week'; // week, month
    public $currentDate;
    
    // Filters
    public $filterBranch = '';
    public $filterUser = '';
    public $filterType = '';
    
    // Create/Edit Modal
    public bool $showModal = false;
    public ?int $editingId = null;
    
    // Form fields
    public $form = [
        'user_id' => '',
        'title' => '',
        'description' => '',
        'type' => 'piket',
        'location' => '',
        'schedule_date' => '',
        'start_time' => '',
        'end_time' => '',
        'shift' => '',
        'is_recurring' => false,
        'recurrence_pattern' => '',
        'recurrence_days' => [],
        'recurrence_end_date' => '',
        'notes' => '',
    ];

    protected $rules = [
        'form.user_id' => 'required|exists:users,id',
        'form.title' => 'required|string|max:255',
        'form.type' => 'required',
        'form.schedule_date' => 'required|date',
    ];

    public function mount()
    {
        $this->currentDate = now();
        $user = auth()->user();
        
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            $this->filterBranch = $user->branch_id;
        }
    }

    public function previousPeriod()
    {
        $this->currentDate = Carbon::parse($this->currentDate)
            ->sub($this->viewMode === 'week' ? '1 week' : '1 month');
    }

    public function nextPeriod()
    {
        $this->currentDate = Carbon::parse($this->currentDate)
            ->add($this->viewMode === 'week' ? '1 week' : '1 month');
    }

    public function goToday()
    {
        $this->currentDate = now();
    }

    public function openCreateModal($date = null)
    {
        $this->resetForm();
        $this->form['schedule_date'] = $date ?? now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal($scheduleId)
    {
        $schedule = StaffSchedule::find($scheduleId);
        if (!$schedule) return;

        $this->editingId = $scheduleId;
        $this->form = [
            'user_id' => $schedule->user_id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'type' => $schedule->type,
            'location' => $schedule->location,
            'schedule_date' => $schedule->schedule_date->format('Y-m-d'),
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'shift' => $schedule->shift,
            'is_recurring' => $schedule->is_recurring,
            'recurrence_pattern' => $schedule->recurrence_pattern,
            'recurrence_days' => $schedule->recurrence_days ?? [],
            'recurrence_end_date' => $schedule->recurrence_end_date?->format('Y-m-d'),
            'notes' => $schedule->notes,
        ];
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form = [
            'user_id' => '',
            'title' => '',
            'description' => '',
            'type' => 'piket',
            'location' => '',
            'schedule_date' => now()->format('Y-m-d'),
            'start_time' => '',
            'end_time' => '',
            'shift' => '',
            'is_recurring' => false,
            'recurrence_pattern' => '',
            'recurrence_days' => [],
            'recurrence_end_date' => '',
            'notes' => '',
        ];
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        $data = array_filter($this->form, fn($v) => $v !== '' && $v !== null);
        $data['branch_id'] = $this->filterBranch ?: $user->branch_id;
        $data['created_by'] = $user->id;

        // Handle recurrence days
        if (!empty($data['recurrence_days'])) {
            $data['recurrence_days'] = array_map('intval', $data['recurrence_days']);
        }

        if ($this->editingId) {
            $schedule = StaffSchedule::find($this->editingId);
            $schedule->update($data);
        } else {
            // If recurring, create multiple entries
            if (!empty($data['is_recurring']) && !empty($data['recurrence_pattern'])) {
                $this->createRecurringSchedules($data);
            } else {
                StaffSchedule::create($data);
            }
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Jadwal berhasil diperbarui' : 'Jadwal berhasil dibuat');
    }

    protected function createRecurringSchedules(array $data)
    {
        $startDate = Carbon::parse($data['schedule_date']);
        $endDate = !empty($data['recurrence_end_date']) 
            ? Carbon::parse($data['recurrence_end_date']) 
            : $startDate->copy()->addMonths(3);

        $dates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $shouldAdd = match ($data['recurrence_pattern']) {
                'daily' => true,
                'weekly' => empty($data['recurrence_days']) || in_array($current->dayOfWeekIso, $data['recurrence_days']),
                'biweekly' => (empty($data['recurrence_days']) || in_array($current->dayOfWeekIso, $data['recurrence_days'])) 
                    && $current->weekOfYear % 2 === $startDate->weekOfYear % 2,
                'monthly' => $current->day === $startDate->day,
                default => false,
            };

            if ($shouldAdd) {
                $dates[] = $current->format('Y-m-d');
            }

            $current->addDay();
        }

        // Limit to reasonable number
        $dates = array_slice($dates, 0, 100);

        foreach ($dates as $date) {
            $scheduleData = $data;
            $scheduleData['schedule_date'] = $date;
            StaffSchedule::create($scheduleData);
        }
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = StaffSchedule::find($scheduleId);
        if ($schedule) {
            $schedule->delete();
            session()->flash('success', 'Jadwal dihapus');
        }
    }

    public function markComplete($scheduleId)
    {
        $schedule = StaffSchedule::find($scheduleId);
        if ($schedule) {
            $schedule->update(['status' => 'completed']);
        }
    }

    // Color helpers for view
    public function getColorBg($color): string
    {
        return match($color) {
            'blue' => '#dbeafe',
            'indigo' => '#e0e7ff',
            'emerald' => '#d1fae5',
            'violet' => '#ede9fe',
            'amber' => '#fef3c7',
            'cyan' => '#cffafe',
            'rose' => '#ffe4e6',
            'gray' => '#f3f4f6',
            default => '#f3f4f6',
        };
    }

    public function getColorText($color): string
    {
        return match($color) {
            'blue' => '#1e40af',
            'indigo' => '#3730a3',
            'emerald' => '#047857',
            'violet' => '#5b21b6',
            'amber' => '#b45309',
            'cyan' => '#0e7490',
            'rose' => '#be123c',
            'gray' => '#374151',
            default => '#374151',
        };
    }

    public function getColorBorder($color): string
    {
        return match($color) {
            'blue' => '#3b82f6',
            'indigo' => '#6366f1',
            'emerald' => '#10b981',
            'violet' => '#8b5cf6',
            'amber' => '#f59e0b',
            'cyan' => '#06b6d4',
            'rose' => '#f43f5e',
            'gray' => '#9ca3af',
            default => '#9ca3af',
        };
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $isAdmin = in_array($user->role, ['super_admin', 'admin']);

        // Build date range
        $currentDate = Carbon::parse($this->currentDate);
        
        if ($this->viewMode === 'week') {
            $startDate = $currentDate->copy()->startOfWeek();
            $endDate = $currentDate->copy()->endOfWeek();
        } else {
            $startDate = $currentDate->copy()->startOfMonth();
            $endDate = $currentDate->copy()->endOfMonth();
        }

        // Get schedules
        $query = StaffSchedule::with(['user', 'branch'])
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->orderBy('schedule_date')
            ->orderBy('start_time');

        if ($this->filterBranch) {
            $query->where('branch_id', $this->filterBranch);
        }
        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        $schedules = $query->get();

        // Group by date
        $schedulesByDate = $schedules->groupBy(fn($s) => $s->schedule_date->format('Y-m-d'));

        // Build calendar days
        $calendarDays = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $calendarDays[] = [
                'date' => $current->copy(),
                'schedules' => $schedulesByDate[$dateKey] ?? collect(),
                'isToday' => $current->isToday(),
                'isCurrentMonth' => $current->month === $currentDate->month,
            ];
            $current->addDay();
        }

        // Get users for dropdown
        $usersQuery = User::whereIn('role', ['admin', 'super_admin', 'pustakawan', 'librarian'])
            ->orderBy('name');
        if (!$isSuperAdmin && $this->filterBranch) {
            $usersQuery->where('branch_id', $this->filterBranch);
        }
        $users = $usersQuery->get();

        // Stats
        $stats = [
            'total_this_period' => $schedules->count(),
            'my_schedules' => $schedules->where('user_id', $user->id)->count(),
            'today_schedules' => StaffSchedule::whereDate('schedule_date', today())
                ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
                ->count(),
            'pending_swaps' => 0, // TODO: implement swap requests count
        ];

        // Get branches
        $branches = $isSuperAdmin ? Branch::orderBy('name')->get() : collect();

        return view('livewire.staff.task.schedule-manager', [
            'calendarDays' => $calendarDays,
            'schedules' => $schedules,
            'schedulesByDate' => $schedulesByDate,
            'users' => $users,
            'branches' => $branches,
            'stats' => $stats,
            'types' => StaffSchedule::getTypes(),
            'locations' => StaffSchedule::getLocations(),
            'shifts' => StaffSchedule::getShifts(),
            'currentDate' => $currentDate,
            'currentMonth' => $currentDate->locale('id')->isoFormat('MMM'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
        ])->extends('staff.layouts.app')->section('content');
    }
}
