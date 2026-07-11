<?php

namespace App\Contracts;

interface AISProviderInterface
{
    public function getName(): string;
    
    public function getVesselPosition(string $imoOrMmsi): ?array;
    
    public function getFleetPositions(array $imoOrMmsiList): array;
    
    public function getVesselDetails(string $imoOrMmsi): ?array;
}
