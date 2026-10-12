<?php


declare(strict_types=1);


namespace App\Services\Ai;


use App\Services\Ai\Contracts\AiScanServiceContract;
use InvalidArgumentException;


class AiScanServiceManager
{
    protected array $drivers = [];


    public function __construct()
    {
        $this->drivers = [
            'fastapi' => FastApiAiScanService::class,
            'fake' => FakeAiScanService::class,
        ];
    }


    /**
     * Get AI scan service driver
     */
    public function driver(?string $driver = null): AiScanServiceContract
    {
        $driver = $driver ?? config('services.ai.driver', 'fake');


        if (!isset($this->drivers[$driver])) {
            throw new InvalidArgumentException("AI driver [{$driver}] not supported.");
        }


        $driverClass = $this->drivers[$driver];


        return app($driverClass);
    }


    /**
     * Get default driver
     */
    public function getDefaultDriver(): string
    {
        return config('services.ai.driver', 'fake');
    }
}



