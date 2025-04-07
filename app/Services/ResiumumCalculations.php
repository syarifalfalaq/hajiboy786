<?php

namespace App\Services;

use App\Models\Rate;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Resiumum;
use Illuminate\Support\Facades\Log;

class ResiumumCalculations
{
    public static function calculateTariff(Get $get, Set $set): void
    {
        $tyCustBefore = $get('ty_cust');
        // Logika perhitungan tarif...
        $tyCustAfter = $get('ty_cust');

        if ($tyCustBefore !== $tyCustAfter) {
            Log::warning('ty_cust changed unexpectedly', [
                'before' => $tyCustBefore,
                'after' => $tyCustAfter,
            ]);
            // Kembalikan nilai ty_cust ke nilai sebelumnya jika berubah
            $set('ty_cust', $tyCustBefore);
        }

        //-- 04/04/2025

        /////////////
        $rate = Rate::where('customer_id', $get('ty_cust'))
            ->where('service_id', $get('layanan_id'))
            ->where('orig_regency_id', $get('orig_regency_id'))
            ->where('province_id', $get('province_id'))
            ->where('regency_id', $get('regency_id'))
            ->where('district_id', $get('district_id'))
            ->first();

        if ($rate) {
            $deskBrg = $get('satuan_barang');
            $effectiveWeight = max(
                floatval(str_replace(['.', ','], ['', '.'], $get('weight'))),
                floatval(str_replace(['.', ','], ['', '.'], $get('total_weight_volume')))
            );

            $tariff = match ($deskBrg) {
                'rate_kg' => $rate->rate_kg * $effectiveWeight,
                'rate_koli' => $rate->rate_koli * intval($get('koli')),
                'rate_pc' => $rate->rate_pc * intval($get('pcs_count')),
                default => 0,
            };

            $tarifBulat = round($tariff, -1);
            $tarifFormat = number_format($tarifBulat, 0, ',', '.');

            // Set tarif yang diformat untuk ditampilkan
            $set('tariff', $tarifFormat);

            // Simpan nilai tarif sebenarnya ke database
            if ($resiumum = Resiumum::find($get('id'))) {
                $resiumum->tariff = $tarifBulat;
                $resiumum->save();
            }
        } else {
            $set('tariff', '0');

            // Simpan tarif nol ke database
            if ($resiumum = Resiumum::find($get('id'))) {
                $resiumum->tariff = 0;
                $resiumum->save();
            }
        }
    }

    public static function calculateOngkir(Get $get, Set $set): void
    {
        $tariff =  floatval(str_replace(['.', ','], ['', '.'], $get('tariff')));
        $discount = floatval(str_replace(['.', ','], ['', '.'], $get('discount')));
        $ongkir = $tariff - $discount;
        // Set nilai yang diformat untuk ditampilkan (6 digit)
        $set('ongkir', number_format($ongkir, 0, ',', '.'));


        // Simpan nilai numerik mentah ke database
        if ($resiumum = Resiumum::find($get('id'))) {
            $resiumum->ongkir = $ongkir;
            $resiumum->save();
        }

        // Debug
        error_log("ongkir: " . $ongkir);
    }
    public static function calculateOngkirTotal(Get $get, Set $set): void
    {
        $ongkir = floatval(str_replace(['.', ','], ['', '.'], $get('ongkir') ?? '0'));
        $biayaAdmin = floatval(str_replace(['.', ','], ['', '.'], $get('biaya_admin') ?? '0'));
        $biayaPacking = floatval(str_replace(['.', ','], ['', '.'], $get('biaya_packing') ?? '0'));
        $biayaTambahan = floatval(str_replace(['.', ','], ['', '.'], $get('biaya_tambahan') ?? '0'));
        $insuranceCost = floatval(str_replace(['.', ','], ['', '.'], $get('insurance_cost') ?? '0'));


        $total = $ongkir + $biayaAdmin + $biayaPacking + $biayaTambahan + $insuranceCost;

        $set('total', number_format($total, 0, ',', '.'));

        // Menyimpan nilai total ke database
        if ($resiumum = Resiumum::find($get('id'))) {
            $resiumum->total = $total;
            $resiumum->save();
        }


        // Debug
        error_log("Total: " . $total);
    }
}
