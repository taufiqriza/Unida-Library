<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberDevice;
use App\Models\MemberNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;
    protected bool $enabled = false;

    public function __construct()
    {
        $credentialsFile = config('firebase.credentials.file');
        
        if ($credentialsFile && file_exists($credentialsFile)) {
            try {
                $factory = (new Factory)->withServiceAccount($credentialsFile);
                $this->messaging = $factory->createMessaging();
                $this->enabled = true;
            } catch (\Exception $e) {
                Log::warning('Firebase initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (!$this->enabled) {
            return $this->sendViaLegacy($fcmToken, $title, $body, $data);
        }

        try {
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM send failed: ' . $e->getMessage());
            
            // Remove invalid token
            if (str_contains($e->getMessage(), 'not a valid FCM registration token')) {
                MemberDevice::where('fcm_token', $fcmToken)->delete();
            }
            
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToDevices(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        if (empty($fcmTokens)) {
            return ['success' => 0, 'failure' => 0];
        }

        if (!$this->enabled) {
            $success = 0;
            foreach ($fcmTokens as $token) {
                if ($this->sendViaLegacy($token, $title, $body, $data)) {
                    $success++;
                }
            }
            return ['success' => $success, 'failure' => count($fcmTokens) - $success];
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $fcmTokens);
            
            // Remove invalid tokens
            if ($report->hasFailures()) {
                foreach ($report->failures()->getItems() as $failure) {
                    if (str_contains($failure->error()->getMessage(), 'not a valid FCM')) {
                        MemberDevice::where('fcm_token', $failure->target()->value())->delete();
                    }
                }
            }

            return [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
            ];
        } catch (\Exception $e) {
            Log::error('FCM multicast failed: ' . $e->getMessage());
            return ['success' => 0, 'failure' => count($fcmTokens)];
        }
    }

    /**
     * Send notification to a member (all their devices)
     */
    public function sendToMember(Member $member, string $title, string $body, array $data = [], string $type = 'general'): bool
    {
        // Save to database
        MemberNotification::create([
            'member_id' => $member->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        // Get all FCM tokens for this member
        $tokens = $member->devices()->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            return true; // Notification saved, but no devices to push
        }

        $result = $this->sendToDevices($tokens, $title, $body, array_merge($data, ['type' => $type]));
        
        return $result['success'] > 0;
    }

    /**
     * Send via Legacy HTTP API (fallback)
     */
    protected function sendViaLegacy(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $serverKey = config('firebase.fcm_server_key');
        
        if (!$serverKey) {
            Log::warning('FCM Server Key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM Legacy send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if Firebase is properly configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled || config('firebase.fcm_server_key');
    }
}
