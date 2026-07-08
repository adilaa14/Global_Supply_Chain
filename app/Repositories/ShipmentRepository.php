<?php

namespace App\Repositories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Collection;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function getAll(): Collection
    {
        return Shipment::with(['originCountry', 'destinationCountry', 'originPort', 'destinationPort'])->get();
    }

    public function findById(string $uuid): ?Shipment
    {
        return Shipment::with(['histories', 'ship', 'container'])->find($uuid);
    }

    public function create(array $data): Shipment
    {
        return Shipment::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $shipment = Shipment::find($uuid);
        if (!$shipment) {
            return false;
        }
        return $shipment->update($data);
    }

    public function delete(string $uuid): bool
    {
        $shipment = Shipment::find($uuid);
        if (!$shipment) {
            return false;
        }
        return $shipment->delete();
    }
}
