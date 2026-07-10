<?php

namespace App\Repositories;

use App\Models\ShipmentDocument;

class DocumentRepository
{
    public function create(array $data)
    {
        return ShipmentDocument::create($data);
    }
}
