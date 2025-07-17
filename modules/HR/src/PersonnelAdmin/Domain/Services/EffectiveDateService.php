<?php

namespace Modules\HR\PersonnelAdmin\Domain\Services;

use Carbon\Carbon;

class EffectiveDateService
{
    public function getPreviousDay(string $date): string
    {
        return Carbon::parse($date)->subDay()->toDateString();
    }

    public function arePeriodsOverlapping(string $start1, string $end1, string $start2, string $end2): bool
    {
        $start1 = Carbon::parse($start1);
        $end1 = Carbon::parse($end1);
        $start2 = Carbon::parse($start2);
        $end2 = Carbon::parse($end2);

        return $start1->between($start2, $end2) || $end1->between($start2, $end2) ||
               $start2->between($start1, $end1) || $end2->between($start1, $end1);
    }
}
