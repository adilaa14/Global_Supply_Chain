<?php

namespace App\Services;

use App\Repositories\DocumentRepository;
use App\Models\ShipmentDocument;

class ShipmentDocumentService
{
    protected DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function attachDocument(array $data): ShipmentDocument
    {
        return $this->documentRepository->create($data);
    }
}
