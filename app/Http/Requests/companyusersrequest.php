<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class companyusersrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "email" => "required|email|unique:companyusers,email",
            "password" => "required|string|min:8",
            "role" => "required|in:Manager,Employee,Admin,Driver",
            "company_id" => "required|exists:company,id",
            "latitude" => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "created_by" => "nullable|exists:companyusers,id",
            "recipient_id" => "nullable|exists:companyusers,id",
            "speed" => "nullable|numeric|min:0",

           
        ];
    }
}
