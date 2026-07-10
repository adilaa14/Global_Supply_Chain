<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'shipment_code'       => $this->shipment_code,
            'shipment_type'       => ucfirst($this->shipment_type),
            'status'              => ucfirst($this->status),
            'risk_score'          => $this->risk_score,
            'progress_percentage' => $this->progress_percentage ?? 0,
            
            'origin' => [
                'country' => $this->whenLoaded('originCountry', fn() => $this->originCountry->name),
                'port'    => $this->whenLoaded('originPort', fn() => $this->originPort->name),
            ],
            
            'destination' => [
                'country' => $this->whenLoaded('destinationCountry', fn() => $this->destinationCountry->name),
                'port'    => $this->whenLoaded('destinationPort', fn() => $this->destinationPort->name),
            ],
            
            'tracking' => [
                'current_latitude'   => (float) $this->latitude,
                'current_longitude'  => (float) $this->longitude,
                'current_ocean'      => $this->current_ocean ?? 'Unknown',
                'ship_speed'         => (float) ($this->ship_speed ?? 0),
                'heading_direction'  => (float) ($this->heading_direction ?? 0),
                'distance_remaining' => (float) ($this->distance_remaining ?? 0),
            ],

            'costs' => [
                'shipping'  => (float) $this->shipping_cost,
                'insurance' => (float) $this->insurance_cost,
                'total'     => (float) ($this->shipping_cost + $this->insurance_cost)
            ],

            'timing' => [
                'departure_date'    => $this->departure_date ? $this->departure_date->toIso8601String() : null,
                'estimated_arrival' => $this->estimated_arrival ? $this->estimated_arrival->toIso8601String() : null,
                'actual_arrival'    => $this->actual_arrival ? $this->actual_arrival->toIso8601String() : null,
                'estimated_delay'   => $this->estimated_delay ?? 0,
            ],
            
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
