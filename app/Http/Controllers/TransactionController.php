<?php

namespace App\Http\Controllers;


use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\GeneralRequest;
use Illuminate\Http\JsonResponse;



class TransactionController extends Controller
{
    public function initiateTransaction(Request $request): JsonResponse
    {

        //     // Check if the user is authenticated
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        $transaction = Transaction::create([
            'user_id' => auth()->id(),  
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference' => Str::uuid(), // Generates a unique reference

            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Transaction successful', 
            'transaction' => $transaction
         ], 201);
    }
   
    
}
