<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Use Policies in controller
    }

    public function rules(): array
    {
        return [
            'shipment_type'          => 'required|in:import,export',
            'origin_country_id'      => 'required|uuid|exists:countries,id',
            'destination_country_id' => 'required|uuid|exists:countries,id',
            'origin_port_id'         => 'required|uuid|exists:ports,id',
            'destination_port_id'    => 'required|uuid|exists:ports,id',
            'ship_id'                => 'nullable|uuid|exists:ships,id',
            'container_id'           => 'nullable|uuid|exists:containers,id',
            'cargo_type'             => 'nullable|string|max:255',
            'cargo_weight'           => 'nullable|numeric|min:0',
            'cargo_volume'           => 'nullable|numeric|min:0',
            'shipping_cost'          => 'nullable|numeric|min:0',
            'insurance_cost'         => 'nullable|numeric|min:0',
            'estimated_arrival'      => 'nullable|date|after_or_equal:today',
        ];
    }
}
