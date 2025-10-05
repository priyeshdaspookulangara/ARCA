<?php

namespace Modules\Fina\TR\Domain;

use Modules\Fina\TR\Domain\Repositories\LiquidityForecastRepository;

class LiquidityForecastService
{
    private LiquidityForecastRepository $liquidityForecastRepository;

    public function __construct(LiquidityForecastRepository $liquidityForecastRepository)
    {
        $this->liquidityForecastRepository = $liquidityForecastRepository;
    }

    public function createLiquidityForecast(array $data): LiquidityForecast
    {
        $liquidityForecast = new LiquidityForecast($data);
        $this->liquidityForecastRepository->save($liquidityForecast);
        return $liquidityForecast;
    }

    public function getLiquidityForecast(int $id): ?LiquidityForecast
    {
        return $this->liquidityForecastRepository->findById($id);
    }

    public function getLiquidityForecastByDate(\DateTime $date): ?LiquidityForecast
    {
        return $this->liquidityForecastRepository->findByDate($date);
    }

    public function getAllLiquidityForecasts()
    {
        return $this->liquidityForecastRepository->getAll();
    }

    public function updateLiquidityForecast(int $id, array $data): ?LiquidityForecast
    {
        $liquidityForecast = $this->liquidityForecastRepository->findById($id);
        if ($liquidityForecast) {
            $liquidityForecast->fill($data);
            $this->liquidityForecastRepository->save($liquidityForecast);
        }
        return $liquidityForecast;
    }

    public function deleteLiquidityForecast(int $id): bool
    {
        $liquidityForecast = $this->liquidityForecastRepository->findById($id);
        if ($liquidityForecast) {
            $this->liquidityForecastRepository->delete($liquidityForecast);
            return true;
        }
        return false;
    }
}