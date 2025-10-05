<?php

namespace Modules\Fina\TR\Infrastructure;

use Modules\Fina\TR\Domain\LiquidityForecast;
use Modules\Fina\TR\Domain\Repositories\LiquidityForecastRepository;
use Illuminate\Support\Collection;

class LiquidityForecastRepositoryImpl implements LiquidityForecastRepository
{
    public function findById(int $id): ?LiquidityForecast
    {
        return LiquidityForecast::find($id);
    }

    public function findByDate(\DateTime $date): ?LiquidityForecast
    {
        return LiquidityForecast::where('forecast_date', $date->format('Y-m-d'))->first();
    }

    public function getAll(): Collection
    {
        return LiquidityForecast::all();
    }

    public function save(LiquidityForecast $liquidityForecast): void
    {
        $liquidityForecast->save();
    }

    public function delete(LiquidityForecast $liquidityForecast): void
    {
        $liquidityForecast->delete();
    }
}