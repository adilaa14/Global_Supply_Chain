<?php

namespace App\Repositories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Collection;

interface ShipmentRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(string $uuid): ?Shipment;
    public function create(array $data): Shipment;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
}
