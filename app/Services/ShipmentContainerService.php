<?php

namespace App\Services;

use App\Repositories\ContainerRepository;
use App\Models\ShipmentContainer;

class ShipmentContainerService
{
    protected ContainerRepository $containerRepository;

    public function __construct(ContainerRepository $containerRepository)
    {
        $this->containerRepository = $containerRepository;
    }

    public function addContainerToShipment(array $data): ShipmentContainer
    {
        return $this->containerRepository->create($data);
    }
}
