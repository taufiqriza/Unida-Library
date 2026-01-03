<?php

namespace App\Services\Circulation;

use App\Models\{Fine, FinePayment, Member};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinePaymentService
{
    public function __construct(protected NotificationService $notificationService) {}

    public function calculateMemberFines(Member $member): array
    {
        $fines = Fine::where('member_id', $member->id)->where('is_paid', false)->get();
        
        return [
            'fines' => $fines,
            'total' => $fines->sum('amount'),
            'count' => $fines->count(),
        ];
    }

    public function createPayment(Member $member, array $fineIds, string $method = 'cash'): array
    {
        $fines = Fine::whereIn('id', $fineIds)
            ->where('member_id', $member->id)
            ->where('is_paid', false)
            ->get();

        if ($fines->isEmpty()) {
            return ['success' => false, 'message' => 'Tidak ada denda yang dipilih'];
        }

        $amount = $fines->sum('amount');
        $branchId = $fines->first()->branch_id ?? $member->branch_id ?? 1;

        $payment = FinePayment::create([
            'member_id' => $member->id,
            'branch_id' => $branchId,
            'amount' => $amount,
            'payment_method' => $method,
            'fine_ids' => $fineIds,
            'expired_at' => now()->addHours(24),
        ]);

        if ($method === 'midtrans') {
            return $this->createMidtransPayment($payment);
        }

        return [
            'success' => true,
            'payment' => $payment,
            'message' => 'Pembayaran dibuat',
        ];
    }

    public function processManualPayment(FinePayment $payment, int $processedBy, string $notes = null): array
    {
        if ($payment->isPaid()) {
            return ['success' => false, 'message' => 'Pembayaran sudah diproses'];
        }

        $payment->update(['processed_by' => $processedBy, 'notes' => $notes]);
        $payment->markAsPaid('cash');

        $this->notificationService->sendPaymentSuccess($payment);

        return ['success' => true, 'message' => 'Pembayaran berhasil diproses'];
    }

    protected function createMidtransPayment(FinePayment $payment): array
    {
        // Midtrans integration - simplified
        $serverKey = config('services.midtrans.server_key');
        
        if (!$serverKey) {
            return ['success' => false, 'message' => 'Payment gateway tidak dikonfigurasi'];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $payment->payment_code,
                'gross_amount' => (int) $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $payment->member->name,
                'email' => $payment->member->email,
            ],
        ];

        try {
            $auth = base64_encode($serverKey . ':');
            $response = \Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
            ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

            if ($response->successful()) {
                $data = $response->json();
                $payment->update([
                    'external_id' => $data['token'] ?? null,
                    'payment_url' => $data['redirect_url'] ?? null,
                    'payment_data' => $data,
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'payment_url' => $data['redirect_url'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans error: ' . $e->getMessage());
        }

        return ['success' => false, 'message' => 'Gagal membuat pembayaran online'];
    }

    public function handleMidtransCallback(array $data): void
    {
        $orderId = $data['order_id'] ?? null;
        $status = $data['transaction_status'] ?? null;

        $payment = FinePayment::where('payment_code', $orderId)->first();
        if (!$payment) return;

        if (in_array($status, ['capture', 'settlement'])) {
            $payment->markAsPaid('midtrans', $data);
            $this->notificationService->sendPaymentSuccess($payment);
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $payment->update(['status' => 'failed', 'payment_data' => $data]);
        }
    }
}
