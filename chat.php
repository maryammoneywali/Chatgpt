<?php
header("Content-Type: application/json");

// Get user input
$data = json_decode(file_get_contents("php://input"), true);
$prompt = $data["prompt"] ?? "";

// Check if input is empty
if (!$prompt) {
    echo json_encode(["response" => "Please enter a message."]);
    exit;
}

// Replace with your Gemini API key
$apiKey = "AIzaSyBiaQzzSk0EwMq3dpXuBbGyZJCsUUOpOIc";  

// Correct API endpoint
$url = "$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

// Correct payload format
$payload = json_encode([
    "contents" => [[ "parts" => [["text" => $prompt]] ]]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
curl_close($ch);

// Decode response
$responseData = json_decode($response, true);

// Check if the API returned a valid response
$botMessage = $responseData["candidates"][0]["content"]["parts"][0]["text"] ?? "I Don't know the hell you're saying, try again later";

// Send response to frontend
file_put_contents("debug_log.txt", json_encode($responseData, JSON_PRETTY_PRINT));
echo json_encode(["debug_response" => $responseData]);
?>
