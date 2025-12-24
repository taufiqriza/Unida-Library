<?php
/**
 * Kubuku API Test Script
 * Run this on VM: php test_kubuku_api.php
 */

$apiKey = 'N2NhZjVlMjJlYTNlYjgxNzVhYjUxODQyOWM4NTg5YTQ6NjYwNw==';
$baseUrl = 'https://api.kubuku.id/api/v1';

function callKubukuApi($endpoint, $apiKey, $method = 'GET', $data = null) {
    $url = "https://api.kubuku.id/api/v1/{$endpoint}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $apiKey,
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'response' => json_decode($response, true) ?: $response,
        'error' => $error,
    ];
}

echo "=== Kubuku API Test ===\n\n";

// Test 1: Get ebooks list
echo "1. Testing GET /ebooks...\n";
$result = callKubukuApi('ebooks', $apiKey);
echo "   HTTP Code: {$result['code']}\n";
if ($result['code'] == 200) {
    $data = $result['response'];
    if (is_array($data)) {
        echo "   ✅ Success! Found " . (isset($data['data']) ? count($data['data']) : count($data)) . " ebooks\n";
        echo "   Sample: " . json_encode(array_slice($data['data'] ?? $data, 0, 2), JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "   ❌ Error: " . json_encode($result['response']) . "\n";
}

echo "\n";

// Test 2: Get categories
echo "2. Testing GET /categories...\n";
$result = callKubukuApi('categories', $apiKey);
echo "   HTTP Code: {$result['code']}\n";
if ($result['code'] == 200) {
    echo "   ✅ Success!\n";
    echo "   Response: " . substr(json_encode($result['response']), 0, 300) . "...\n";
} else {
    echo "   Response: " . json_encode($result['response']) . "\n";
}

echo "\n";

// Test 3: Get publishers
echo "3. Testing GET /publishers...\n";
$result = callKubukuApi('publishers', $apiKey);
echo "   HTTP Code: {$result['code']}\n";
if ($result['code'] == 200) {
    echo "   ✅ Success!\n";
} else {
    echo "   Response: " . json_encode($result['response']) . "\n";
}

echo "\n=== Done ===\n";
