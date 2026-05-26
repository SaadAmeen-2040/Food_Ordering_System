<?php
// chatbot_api.php
require_once 'includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

// Get the raw POST data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!isset($input['message'])) {
    echo json_encode(['error' => 'Message is required.']);
    exit;
}

$userMessage = $input['message'];
$history = isset($input['history']) ? $input['history'] : [];

// Format the history for Gemini API
$contents = [];
foreach ($history as $msg) {
    $contents[] = [
        "role" => $msg['role'] === 'bot' ? 'model' : 'user',
        "parts" => [["text" => $msg['content']]]
    ];
}

// Add the current user message
$contents[] = [
    "role" => "user",
    "parts" => [["text" => $userMessage]]
];

$data = [
    "contents" => $contents,
    "systemInstruction" => [
        "role" => "user",
        "parts" => [
            [
                "text" => "You are a helpful, friendly customer support assistant for 'Foodies', an online food ordering system. Answer questions about the menu, delivery, and orders. Be concise and polite."
            ]
        ]
    ]
];

$apiKey = GEMINI_API_KEY;
if ($apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
    echo json_encode(['error' => 'Gemini API key is not configured.']);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'Gemini API error', 'details' => json_decode($response)]);
    exit;
}

$responseData = json_decode($response, true);

if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $botReply = $responseData['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['reply' => $botReply]);
} else {
    echo json_encode(['error' => 'Invalid response from Gemini API.', 'details' => $responseData]);
}
