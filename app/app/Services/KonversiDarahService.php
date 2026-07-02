<?php

namespace App\Services;

class KonversiDarahService
{
    /**
     * Conversion tables for Kantong Pediatrik.
     * Each entry: volume (ml) => weight (gram)
     */

    // WB, PRC & PCR: Berat Kantong Kosong = 31 gram
    protected static array $wb = [
        50 => 74, 60 => 84, 70 => 95, 80 => 105, 90 => 116,
        100 => 127, 110 => 137, 120 => 148, 130 => 158, 140 => 169, 150 => 179,
    ];

    protected static array $prc_pcr = [
        50 => 76, 60 => 87, 70 => 98, 80 => 109, 90 => 120,
        100 => 131, 110 => 141, 120 => 152, 130 => 163, 140 => 174, 150 => 185,
    ];

    // PCL: Berat Kantong Kosong = 20 gram
    protected static array $pcl = [
        50 => 75, 60 => 86, 70 => 97, 80 => 108, 90 => 119,
        100 => 130, 110 => 140, 120 => 151, 125 => 157,
    ];

    /**
     * Get the conversion table for a given jenis_darah.
     */
    protected static function getTable(string $jenisDarah): ?array
    {
        $jenisDarah = strtoupper(trim($jenisDarah));

        return match ($jenisDarah) {
            'WB' => self::$wb,
            'PRC', 'PCR' => self::$prc_pcr,
            'PCL' => self::$pcl,
            default => null,
        };
    }

    /**
     * Convert gram (weight) to volume (ml) based on jenis_darah.
     * Uses linear interpolation between table entries.
     *
     * @param float $gram   Weight in grams
     * @param string $jenisDarah  Blood product type (WB, PRC, PCR, PCL)
     * @return float|null   Volume in ml, or null if conversion not possible
     */
    public static function convertGramToVolume(float $gram, string $jenisDarah): ?float
    {
        $table = self::getTable($jenisDarah);
        if (!$table) {
            return null;
        }

        // Invert table: gram => volume
        $gramToVolume = [];
        foreach ($table as $vol => $g) {
            $gramToVolume[$g] = $vol;
        }

        // Sort by gram ascending
        ksort($gramToVolume);

        $grams = array_keys($gramToVolume);
        $volumes = array_values($gramToVolume);

        // Exact match
        if (isset($gramToVolume[$gram])) {
            return (float) $gramToVolume[$gram];
        }

        // Below minimum
        if ($gram < $grams[0]) {
            // Extrapolate using first two points
            $g1 = $grams[0];
            $g2 = $grams[1];
            $v1 = $volumes[0];
            $v2 = $volumes[1];
            return round($v1 + ($gram - $g1) * ($v2 - $v1) / ($g2 - $g1), 2);
        }

        // Above maximum
        $lastIdx = count($grams) - 1;
        if ($gram > $grams[$lastIdx]) {
            $g1 = $grams[$lastIdx - 1];
            $g2 = $grams[$lastIdx];
            $v1 = $volumes[$lastIdx - 1];
            $v2 = $volumes[$lastIdx];
            return round($v1 + ($gram - $g1) * ($v2 - $v1) / ($g2 - $g1), 2);
        }

        // Linear interpolation between two nearest entries
        for ($i = 0; $i < count($grams) - 1; $i++) {
            if ($gram >= $grams[$i] && $gram <= $grams[$i + 1]) {
                $g1 = $grams[$i];
                $g2 = $grams[$i + 1];
                $v1 = $volumes[$i];
                $v2 = $volumes[$i + 1];

                $ratio = ($gram - $g1) / ($g2 - $g1);
                return round($v1 + $ratio * ($v2 - $v1), 2);
            }
        }

        return null;
    }

    /**
     * Get the conversion table data for display purposes.
     */
    public static function getConversionTable(): array
    {
        return [
            'WB' => self::$wb,
            'PRC_PCR' => self::$prc_pcr,
            'PCL' => self::$pcl,
        ];
    }
}
