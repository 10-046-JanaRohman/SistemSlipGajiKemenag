<?php

namespace App\Services;

class SlipGajiFormatter
{
    public static function format(array $detail): array
    {
        return SlipGajiCalculator::hitung($detail);
    }
}
