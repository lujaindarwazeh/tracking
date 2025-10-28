<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateVehicle extends FormRequest
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
            "namecar"=>"required|string|max:255",
            "serialnumber"=>"required|string|max:255|unique:vehicle,serialnumber," . $this->route('vehicle'),
            "lastupdatetime"=>"nullable|date",
            "longitude"=>"nullable|numeric",
            "latitude"=>"nullable|numeric",
            "odometer"=>"nullable|numeric",
            "drivername"=>"nullable|string|max:255",


            //
        ];
    }
}
