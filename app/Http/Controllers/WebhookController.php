<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SquadTransaction;



class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Retrieve request body
        $body = $request->getContent();
        $data = json_decode($body, true);

        // Log received webhook for debugging
        Log::info('Squad Webhook Received:', $data);

        // Verify event type (process only successful charges)
        if (!isset($data['Event']) || $data['Event'] !== 'charge_successful') {
            return response()->json(['response_code' => 400, 'response_description' => 'Invalid event'], 400);
        }

        // Get Squad signature from headers
        $squadSignature = $request->header('x-squad-signature');
        if (!$squadSignature) {
            return response()->json(['response_code' => 400, 'response_description' => 'Missing signature'], 400);
        }

        // Get secret key securely
        $secretKey = config('services.squadco.secret_key');

        // Generate the hash for validation
        $computedSignature = hash_hmac('sha512', json_encode($data, JSON_UNESCAPED_SLASHES), $secretKey);

        // Verify the webhook signature
        if ($squadSignature !== $computedSignature) {
            return response()->json(['response_code' => 400, 'response_description' => 'Invalid signature'], 400);
        }

        // Extract transaction details from Body
        $transactionReference = $data['TransactionRef'] ?? null;
        $amount = $data['Body']['amount'] ?? 0;
        $email = $data['Body']['email'] ?? null;
        $transactionDate = $data['Body']['created_at'] ?? null;
        $transactionType = $data['Body']['transaction_type'] ?? null;
        $gatewayRef = $data['Body']['gateway_ref'] ?? null;
        $currency = $data['Body']['currency'] ?? 'NGN';

        // Validate transaction reference
        if (!$transactionReference) {
            return response()->json([
                'response_code' => 400,
                'response_description' => 'Missing transaction reference'
            ], 400);
        }

        // Check for duplicate transaction
        if (SquadTransaction::where('transaction_ref', $transactionReference)->exists()) {
            return response()->json([
                'response_code' => 200,
                'response_description' => 'Duplicate transaction ignored'
            ], 200);
        }

        // Save transaction to database
        SquadTransaction::create([
            'transaction_ref' => $transactionReference,
            'amount' => $amount,
            'customer_identifier' => $email,
            'transaction_date' => $transactionDate,
            'transaction_type' => $transactionType,
            'gateway_ref' => $gatewayRef,
            'currency' => $currency
        ]);

        // Return success response to Squad
        return response()->json([
            'response_code' => 200,
            'transaction_reference' => $transactionReference,
            'response_description' => 'Transaction recorded successfully'
        ], 200);
    }
}


