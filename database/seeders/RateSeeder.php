<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Enums\CustomerType;
use App\Enums\JenisLayanan;
use App\Enums\JenisBarang;
//---php artisan db:seed --class=RateSeeder
class RateSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get IDs for Banjarmasin and Palangkaraya
        $banjarmasinId = DB::table('regencies')->where('name', 'LIKE', '%Banjarmasin%')->value('id');
        $palangkarayaId = DB::table('regencies')->where('name', 'LIKE', '%Palangkaraya%')->value('id');

        if (!$banjarmasinId || !$palangkarayaId) {
            throw new \Exception('Could not find Banjarmasin or Palangkaraya in the regencies table.');
        }

        $kotaAsal = [$banjarmasinId, $palangkarayaId];

        // Get IDs for Kalimantan Selatan and Kalimantan Tengah provinces
        $kalselId = DB::table('provinces')->where('name', 'LIKE', '%Kalimantan Selatan%')->value('id');
        $kaltengId = DB::table('provinces')->where('name', 'LIKE', '%Kalimantan Tengah%')->value('id');

        if (!$kalselId || !$kaltengId) {
            throw new \Exception('Could not find Kalimantan Selatan or Kalimantan Tengah in the provinces table.');
        }

        // Get all regencies in Kalimantan Selatan and Kalimantan Tengah
        $tujuanRegencies = DB::table('regencies')
            ->whereIn('province_id', [$kalselId, $kaltengId])
            ->pluck('id')
            ->toArray();

        foreach (range(1, 20) as $index) {
            $asalId = $faker->randomElement($kotaAsal);
            $tujuanId = $faker->randomElement($tujuanRegencies);

            // Get province_id based on destination regency_id
            $provinceId = DB::table('regencies')->where('id', $tujuanId)->value('province_id');

            // Get a random district_id that matches the destination regency_id
            $districtId = DB::table('districts')
                ->where('regency_id', $tujuanId)
                ->inRandomOrder()
                ->value('id');

            if (!$districtId) {
                throw new \Exception("No district found for regency_id: $tujuanId");
            }

            DB::table('rates')->insert([
                'customer_id' => $faker->randomElement([CustomerType::UMUM->value, CustomerType::LANGGANAN->value]),
                'service_id' => $faker->randomElement([JenisLayanan::REGULER->value, JenisLayanan::EXPRESS->value]),
                'pack_type_id' => $faker->randomElement([JenisBarang::PACKAGE->value, JenisBarang::DOCUMENT->value]),
                'orig_regency_id' => $asalId,
                'regency_id' => $tujuanId,
                'district_id' => $districtId,
                'province_id' => $provinceId,
                'rate_kg' => $faker->numberBetween(10000, 100000),
                'rate_pc' => $faker->numberBetween(5000, 50000),
                'rate_koli' => $faker->numberBetween(50000, 500000),
                'min_weight' => $faker->numberBetween(1, 5),
                'max_weight' => $faker->numberBetween(20, 100),
                'etd' => $faker->numberBetween(1, 3) . '-' . $faker->numberBetween(3, 5) . ' hari',
                'discount' => $faker->optional(0.3)->numberBetween(5, 20),
                'add_cost' => $faker->optional(0.5)->numberBetween(5000, 50000),
                'notes' => $faker->optional(0.7)->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
