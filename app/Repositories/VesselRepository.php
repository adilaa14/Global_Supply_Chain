<?php

namespace App\Repositories;

use App\Models\Vessel;
use Illuminate\Support\Collection;

class VesselRepository
{
    public function getAllActiveVessels(): Collection
    {
        return Vessel::where('status', 'Active')
            ->whereHas('shipments', function ($query) {
                $query->whereIn('status', ['Preparing', 'In Transit']);
            })
            ->with(['latestPosition', 'activeRoute.destinationPort'])
            ->get();
    }

    public function findById(string $id): ?Vessel
    {
        return Vessel::with(['positions', 'routes', 'shipments.originPort', 'shipments.destinationPort'])
            ->findOrFail($id);
    }

    public function findByImo(string $imo): ?Vessel
    {
        return Vessel::where('imo_number', $imo)->first();
    }

    public function create(array $data): Vessel
    {
        return Vessel::create($data);
    }

    public function update(Vessel $vessel, array $data): Vessel
    {
        $vessel->update($data);
        return $vessel;
    }
}
