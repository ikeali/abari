<?php

header("Content-Type: application/json");

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

// Check if the signature exists in headers
$signatureHeader = $_SERVER['HTTP_X_SQUAD_SIGNATURE'] ?? '';

if (!$signatureHeader) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized - Signature missing"]);
    exit();
}

// Retrieve the request body
$input = file_get_contents("php://input");
$body = json_decode($input, true); // Convert to an array

// Define your secret key (Best to get it from Laravel's environment file)
define('SQUAD_SECRET_KEY', 'sandbox_sk_847b0b93890873ef0305389b0ac4a8fc57c233a03279');

// Generate HMAC SHA-512 hash
$generatedSignature = hash_hmac('sha512', json_encode($body, JSON_UNESCAPED_SLASHES), SQUAD_SECRET_KEY);

// Validate signature
if (!hash_equals($generatedSignature, $signatureHeader)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized - Invalid signature"]);
    exit();
}

// Log the request for debugging (Optional)
file_put_contents('squad_webhook.log', date('Y-m-d H:i:s') . " - Webhook received: " . $input . "\n", FILE_APPEND);

// Send 200 OK response
http_response_code(200);
echo json_encode([
    "response_code"         => 200,
    "transaction_reference" => $body['transaction_reference'] ?? null,
    "response_description"  => "Success"
]);

exit();
?>
