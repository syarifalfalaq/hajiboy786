<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Network;

class NetworkSeeder extends Seeder
{
    public function run()
    {
        $networks = [
            // Central Kalimantan
            [
                'type_id' => 2,
                'code' => '62010000',
                'name' => 'Kantor Cabang Palangkaraya',
                'address' => 'Jl. Ahmad Yani No. 123, Palangkaraya',
                'kordinat' => '-2.2136,113.9108',
                'postal_code' => '73112',
                'phone' => '(0536) 3222111',
                'email' => 'kc.palangkaraya@example.com',
                'orig_province_id' => 62,
                'orig_regency_id' => 6201,
                'orig_district_id' => 620101,
            ],
            [
                'type_id' => 3,
                'code' => '62020000',
                'name' => 'Outlet Sampit',
                'address' => 'Jl. Iskandar No. 45, Sampit',
                'kordinat' => '-2.5329,112.9511',
                'postal_code' => '74312',
                'phone' => '(0531) 21234',
                'email' => 'outlet.sampit@example.com',
                'orig_province_id' => 62,
                'orig_regency_id' => 6202,
                'orig_district_id' => 620201,
            ],
            [
                'type_id' => 3,
                'code' => '62110000',
                'name' => 'Outlet Pangkalan Bun',
                'address' => 'Jl. Pangeran Diponegoro No. 78, Pangkalan Bun',
                'kordinat' => '-2.6841,111.6298',
                'postal_code' => '74111',
                'phone' => '(0532) 21555',
                'email' => 'outlet.pangkalanbun@example.com',
                'orig_province_id' => 62,
                'orig_regency_id' => 6211,
                'orig_district_id' => 621101,
            ],
            [
                'type_id' => 3,
                'code' => '62030000',
                'name' => 'Outlet Kuala Kapuas',
                'address' => 'Jl. Tambun Bungai No. 56, Kuala Kapuas',
                'kordinat' => '-3.0091,114.3824',
                'postal_code' => '73514',
                'phone' => '(0513) 21666',
                'email' => 'outlet.kualakapuas@example.com',
                'orig_province_id' => 62,
                'orig_regency_id' => 6203,
                'orig_district_id' => 620301,
            ],
            [
                'type_id' => 3,
                'code' => '62050000',
                'name' => 'Outlet Kasongan',
                'address' => 'Jl. Cilik Riwut No. 34, Kasongan',
                'kordinat' => '-1.8958,113.4117',
                'postal_code' => '74311',
                'phone' => '(0536) 21777',
                'email' => 'outlet.kasongan@example.com',
                'orig_province_id' => 62,
                'orig_regency_id' => 6205,
                'orig_district_id' => 620501,
            ],
            // South Kalimantan
            [
                'type_id' => 2,
                'code' => '63710000',
                'name' => 'Kantor Cabang Banjarmasin',
                'address' => 'Jl. Lambung Mangkurat No. 89, Banjarmasin',
                'kordinat' => '-3.3186,114.5944',
                'postal_code' => '70111',
                'phone' => '(0511) 3366777',
                'email' => 'kc.banjarmasin@example.com',
                'orig_province_id' => 63,
                'orig_regency_id' => 6371,
                'orig_district_id' => 637101,
            ],
            [
                'type_id' => 3,
                'code' => '63010000',
                'name' => 'Outlet Martapura',
                'address' => 'Jl. Ahmad Yani No. 67, Martapura',
                'kordinat' => '-3.4112,114.8443',
                'postal_code' => '70614',
                'phone' => '(0511) 4721888',
                'email' => 'outlet.martapura@example.com',
                'orig_province_id' => 63,
                'orig_regency_id' => 6301,
                'orig_district_id' => 630101,
            ],
            [
                'type_id' => 3,
                'code' => '63040000',
                'name' => 'Outlet Barabai',
                'address' => 'Jl. Brigjen H. Hasan Basri No. 23, Barabai',
                'kordinat' => '-2.5847,115.3821',
                'postal_code' => '71311',
                'phone' => '(0517) 41999',
                'email' => 'outlet.barabai@example.com',
                'orig_province_id' => 63,
                'orig_regency_id' => 6304,
                'orig_district_id' => 630401,
            ],
            [
                'type_id' => 3,
                'code' => '63060000',
                'name' => 'Outlet Tanjung',
                'address' => 'Jl. Pangeran Antasari No. 12, Tanjung',
                'kordinat' => '-2.1947,115.3500',
                'postal_code' => '71571',
                'phone' => '(0526) 2021000',
                'email' => 'outlet.tanjung@example.com',
                'orig_province_id' => 63,
                'orig_regency_id' => 6306,
                'orig_district_id' => 630601,
            ],
            [
                'type_id' => 3,
                'code' => '63080000',
                'name' => 'Outlet Kotabaru',
                'address' => 'Jl. Veteran No. 55, Kotabaru',
                'kordinat' => '-3.2384,116.2166',
                'postal_code' => '72112',
                'phone' => '(0518) 21111',
                'email' => 'outlet.kotabaru@example.com',
                'orig_province_id' => 63,
                'orig_regency_id' => 6308,
                'orig_district_id' => 630801,
            ],
        ];

        foreach ($networks as $network) {
            $createdNetwork = Network::create($network);
            echo "Created network: " . $createdNetwork->name . "\n";
        }
    }
}

//----------- php artisan db:seed --class=NetworkSeeder -------
