<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// class RegisterRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

    /**
     * Get the validation rules that apply to the request.
     *
    //  * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    //  */
    // public function rules(): array
    // {
    //     return [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:6',
    //     ];
    // }
// }


class GeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set authorization logic if needed
    }

    public function rules(): array
    {
        // Get the current route name
        $route = $this->route()->getName();

        return match ($route) {
            'transaction.create' => [
                'amount' => 'required|numeric|min:1',
                'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
                'reference' => 'required|string|uniquie',
                'status' => 'required|string',
            ],
            'transaction.update' => [
                'status' => 'required|string|in:pending,completed,failed',
            ],
            'transaction.delete' => [
                'transaction_id' => 'required|exists:transactions,id',
            ],
            'user.register' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ],
            'user.login' => [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ],
            default => [],
        };
    }
}
