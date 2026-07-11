<?php

namespace App\Services\AIS;

use App\Contracts\AISProviderInterface;
use InvalidArgumentException;

class AISManager
{
    protected array $providers = [];
    protected string $defaultProvider;

    public function __construct()
    {
        $this->providers = [
            'aisstream' => new AISStreamProvider(),
            'marinetraffic' => new MarineTrafficProvider(),
        ];
        
        $this->defaultProvider = config('services.ais.default', 'aisstream');
    }

    public function provider(?string $name = null): AISProviderInterface
    {
        $name = $name ?: $this->defaultProvider;

        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("AIS Provider [{$name}] is not configured.");
        }

        return $this->providers[$name];
    }
    
    public function switchProvider(string $name): void
    {
        $this->defaultProvider = $name;
    }
}
