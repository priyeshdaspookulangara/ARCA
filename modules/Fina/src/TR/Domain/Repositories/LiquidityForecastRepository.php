<?php

namespace Modules\Fina\TR\Domain\Repositories;

use Modules\Fina\TR\Domain\LiquidityForecast;
use Illuminate\Support\Collection;

interface LiquidityForecastRepository
{
    public function findById(int $id): ?LiquidityForecast;

    public function findByDate(\DateTime $date): ?LiquidityForecast;

    public function getAll(): Collection;

    public function save(LiquidityForecast $liquidityForecast): void;

    public function delete(LiquidityForecast $liquidityForecast): void;
}