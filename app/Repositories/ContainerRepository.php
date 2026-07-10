<?php

namespace App\Repositories;

use App\Models\ShipmentContainer;

class ContainerRepository
{
    public function create(array $data)
    {
        return ShipmentContainer::create($data);
    }
}
