<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SquadTransaction;


class VirtualAccountController extends Controller
{
    public function createVirtualAccount(Request $request)
    {
        // Get the URL and API key from the config
        $url = config('services.squadco.base_url') . '/virtual-account/create-dynamic-virtual-account';

        $apiKey = config('services.squadco.api_key');
        
        // // Validate required parameters
        $validatedData = $request->validate([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
        //     'beneficiary_account' => 'nullable|string|regex:/^\d{10}$/', // Only 10-digit GTBank accounts
        ]);

        // Send the request to Abari Pay API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            // ])->post($url, (object) []); // Correct way to send an empty JSON object
            ])->post($url, $validatedData);


            // Convert response to JSON
            $responseData = $response->json();

            Log::info('Squad API Response:', $responseData);

            
            // Check if the API call was successful
            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $responseData,
                ], 200);
            } else {
                Log::error('Squad API Error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create virtual account',
                    'details' => $responseData,
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Squad API Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while connecting to Squad.',
            ], 500);
        }
    }


    public function initiateDynamicVirtualAccount(Request $request)
    {
        $url = config('services.squadco.base_url') . '/virtual-account/initiate-dynamic-virtual-account';
        $apiKey = config('services.squadco.api_key');

        // Validate request
        $validatedData = $request->validate([
            'amount' => 'required|integer',
            'transaction_ref' => 'required|string',
            'duration' => 'required|integer',
            'email' => 'required|email',
            'pass_charge' => 'boolean'
        ]);

        try {
            Log::info('Making API Request to SquadCo:', ['url' => $url, 'data' => $validatedData]);

            // Make API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $validatedData);

            $responseData = $response->json();

            Log::info('Full Squad API Response:', ['response' => $responseData]);


            if ($response->successful()) {

                return response()->json([
                    'status' => 'success',
                    'data' => $responseData['data'] ?? $responseData,
                ], 200);
            } else {
                Log::error('Squad API Error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to initiate virtual account transaction',
                    'details' => $responseData,
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Squad API Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while connecting to Squad.',
            ], 500);
        }
    }
     

    public function getTransaction($transaction_reference)
    {
        $url = config('services.squadco.base_url') .'/virtual-account/get-dynamic-virtual-account-transactions/{$transaction_reference}';
        $apiKey = config('services.squadco.api_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->get($url);

            $responseData = $response->json();

            // Check if the API request was successful and contains valid data
            if (!$response->successful() || empty($responseData['data'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $responseData['message'] ?? 'Transaction not found',
                    'data' => [
                        'count' => 0,
                        'rows' => []
                    ]
                ], $response->status());
            }

            // Ensure the transactions are available
            $transactions = $responseData['data'];

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Success',
                'data' => [
                    'count' => count($transactions),
                    'rows' => array_map(function ($transaction) {
                        return [
                            'transaction_status' => $transaction['transaction_status'] ?? 'UNKNOWN',
                            'transaction_reference' => $transaction['transaction_reference'] ?? 'N/A',
                            'created_at' => $transaction['created_at'] ?? now()->toIso8601String(),
                            'refund' => $transaction['refund'] ?? false,
                        ];
                    }, $transactions),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching transaction:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the transaction.',
            ], 500);
        }
    }
      

    public function editTransactionAmountDuration(Request $request)
    {

        $url = config('services.squadco.base_url') . '/virtual-account/update-dynamic-virtual-account-time-and-amount';
        $apiKey = config('services.squadco.api_key');


        $validated = $request->validate([
            'transaction_reference' => 'required|string',
            'amount' => 'nullable|integer',
            'duration' => 'nullable|integer'
        ]);

        try {
            // Make a PATCH request to Squad API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->patch($url, $validated);
    
            $responseData = $response->json();
    
            // Check if the request was successful
            if ($response->successful()) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'Success',
                    'data' => $responseData['data'] ?? []
                ]);
            }
    
            // Handle errors based on status code
            return response()->json([
                'status' => $response->status(),
                'success' => false,
                'message' => $responseData['message'] ?? 'Something went wrong'
            ], $response->status());
    
        } catch (\Exception $e) {
            Log::error('Error editing transaction:', ['error' => $e->getMessage()]);
    
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    
    }


    public function simulate_payment(Request $request)
    {
        $url = config('services.squadco.base_url') . '/virtual-account/simulate/payment';
        $apiKey = config('services.squadco.api_key');

        $validated = $request->validate([
            'virtual_account_number' => 'required|string',
            'amount' => 'required|string',
            'dva' => 'required|boolean'
        ]);

        try {
            // Make a PATCH request to Squad API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->post($url, $validated);
    
            $responseData = $response->json();
    
            // Check if the request was successful
            if ($response->successful()) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'Success',
                    'data' => $responseData['data'] ?? []
                ]);
            }
    
            // Handle errors based on status code
            return response()->json([
                'status' => $response->status(),
                'success' => false,
                'message' => $responseData['message'] ?? 'Something went wrong'
            ], $response->status());
    
        } catch (\Exception $e) {
            Log::error('Error simulating transaction:', ['error' => $e->getMessage()]);
    
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }

    }

}


