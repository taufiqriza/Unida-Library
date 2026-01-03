<?php

namespace App\Livewire\Opac\Member;

use App\Models\{Loan, Reservation, Fine, FinePayment, Book};
use App\Services\Circulation\{ReservationService, RenewalService, FinePaymentService};
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyLoans extends Component
{
    public $activeTab = 'loans';
    public $selectedFines = [];
    public $showPaymentModal = false;
    public $paymentMethod = 'cash';

    protected $listeners = ['refreshLoans' => '$refresh'];

    public function getMemberProperty()
    {
        return Auth::guard('member')->user();
    }

    public function getActiveLoansProperty()
    {
        return Loan::with('item.book')
            ->where('member_id', $this->member->id)
            ->where('is_returned', false)
            ->orderBy('due_date')
            ->get();
    }

    public function getLoanHistoryProperty()
    {
        return Loan::with('item.book')
            ->where('member_id', $this->member->id)
            ->where('is_returned', true)
            ->latest('return_date')
            ->limit(20)
            ->get();
    }

    public function getReservationsProperty()
    {
        return Reservation::with('book')
            ->where('member_id', $this->member->id)
            ->active()
            ->orderBy('created_at')
            ->get();
    }

    public function getFinesProperty()
    {
        return Fine::with('loan.item.book')
            ->where('member_id', $this->member->id)
            ->where('is_paid', false)
            ->get();
    }

    public function getTotalFineProperty()
    {
        return $this->fines->sum('amount');
    }

    public function renewLoan(int $loanId)
    {
        $loan = Loan::where('member_id', $this->member->id)->find($loanId);
        if (!$loan) {
            $this->dispatch('notify', type: 'error', message: 'Pinjaman tidak ditemukan');
            return;
        }

        $service = app(RenewalService::class);
        $result = $service->renew($loan, 'online');

        $this->dispatch('notify', 
            type: $result['success'] ? 'success' : 'error', 
            message: $result['message']
        );
    }

    public function cancelReservation(int $reservationId)
    {
        $reservation = Reservation::where('member_id', $this->member->id)->find($reservationId);
        if (!$reservation) {
            $this->dispatch('notify', type: 'error', message: 'Reservasi tidak ditemukan');
            return;
        }

        $service = app(ReservationService::class);
        $result = $service->cancel($reservation);

        $this->dispatch('notify', 
            type: $result['success'] ? 'success' : 'error', 
            message: $result['message']
        );
    }

    public function toggleFine(int $fineId)
    {
        if (in_array($fineId, $this->selectedFines)) {
            $this->selectedFines = array_diff($this->selectedFines, [$fineId]);
        } else {
            $this->selectedFines[] = $fineId;
        }
    }

    public function selectAllFines()
    {
        $this->selectedFines = $this->fines->pluck('id')->toArray();
    }

    public function getSelectedTotalProperty()
    {
        return Fine::whereIn('id', $this->selectedFines)->sum('amount');
    }

    public function openPaymentModal()
    {
        if (empty($this->selectedFines)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih denda yang akan dibayar');
            return;
        }
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        $service = app(FinePaymentService::class);
        $result = $service->createPayment($this->member, $this->selectedFines, $this->paymentMethod);

        if ($result['success']) {
            if ($this->paymentMethod === 'midtrans' && isset($result['payment_url'])) {
                $this->dispatch('redirect-to-payment', url: $result['payment_url']);
            } else {
                $this->dispatch('notify', type: 'success', message: 'Silakan lakukan pembayaran di perpustakaan');
            }
            $this->showPaymentModal = false;
            $this->selectedFines = [];
        } else {
            $this->dispatch('notify', type: 'error', message: $result['message']);
        }
    }

    public function render()
    {
        return view('livewire.opac.member.my-loans')
            ->layout('components.opac.layout', ['title' => 'Pinjaman Saya']);
    }
}
