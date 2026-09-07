<?php

namespace App\Support;

use App\Models\TestSession;

class OrganolepticStatistics
{
    public static function calculate(TestSession $testSession): array
    {
        $panels = $testSession->assessments;

        $n = $panels->count();

        if ($n === 0) {
            return [
                'konstanta' => 1.96,
                'n' => 0,
                'akar_n' => 0,
                's2' => 0,
                's' => 0,
                'p_min' => 0,
                'p_max' => 0,
                'p' => 0,
                'p_bulat' => 0,
            ];
        }

        $rataList = $panels->pluck('nilai_akhir');

        $p = (float) $rataList->average();

        $variance = $rataList->reduce(
            fn ($carry, $v) => $carry + pow($v - $p, 2),
            0
        ) / max($n - 1, 1);

        $stdDev = sqrt($variance);

        $konstanta = 1.96;

        $margin = ($konstanta * $stdDev) / sqrt($n);

        return [
            'konstanta' => $konstanta,
            'n' => $n,
            'akar_n' => sqrt($n),
            's2' => $variance,
            's' => $stdDev,
            'p_min' => $p - $margin,
            'p_max' => $p + $margin,
            'p' => $p,
            'p_bulat' => round($p * 2) / 2,
        ];
    }
}