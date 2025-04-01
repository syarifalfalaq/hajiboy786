<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'nama' => 'Kalteng Express',
                'jenis' => 'kurir',
                'alamat' => 'Jl. Raya Palangkaraya No. 123, Kalimantan Tengah',
                'telepon' => '08123456789',
                'email' => 'kalteng.express@example.com',
            ],
            [
                'nama' => 'Palangkaraya Pickup',
                'jenis' => 'pickup',
                'alamat' => 'Jl. Diponegoro No. 456, Palangkaraya, Kalimantan Tengah',
                'telepon' => '08234567890',
                'email' => 'palangkaraya.pickup@example.com',
            ],
            [
                'nama' => 'Borneo Taxi',
                'jenis' => 'taksi',
                'alamat' => 'Jl. Ahmad Yani No. 789, Palangkaraya, Kalimantan Tengah',
                'telepon' => '08345678901',
                'email' => 'borneo.taxi@example.com',
            ],
            [
                'nama' => 'Dayak Delivery',
                'jenis' => 'pengiriman',
                'alamat' => 'Jl. Tjilik Riwut Km. 5, Kalimantan Tengah',
                'telepon' => '08456789012',
                'email' => 'dayak.delivery@example.com',
            ],
            [
                'nama' => 'Kalteng Ride',
                'jenis' => 'taksi',
                'alamat' => 'Jl. G. Obos No. 101, Palangkaraya, Kalimantan Tengah',
                'telepon' => '08567890123',
                'email' => 'kalteng.ride@example.com',
            ],
            [
                'nama' => 'Banjarmasin Express',
                'jenis' => 'kurir',
                'alamat' => 'Jl. A. Yani Km. 3, Banjarmasin, Kalimantan Selatan',
                'telepon' => '08678901234',
                'email' => 'banjarmasin.express@example.com',
            ],
            [
                'nama' => 'Banjar Pickup',
                'jenis' => 'pickup',
                'alamat' => 'Jl. Lambung Mangkurat No. 202, Banjarmasin, Kalimantan Selatan',
                'telepon' => '08789012345',
                'email' => 'banjar.pickup@example.com',
            ],
            [
                'nama' => 'Kalsel Cab',
                'jenis' => 'taksi',
                'alamat' => 'Jl. Pangeran Antasari No. 303, Banjarmasin, Kalimantan Selatan',
                'telepon' => '08890123456',
                'email' => 'kalsel.cab@example.com',
            ],
            [
                'nama' => 'Martapura Courier',
                'jenis' => 'pengiriman',
                'alamat' => 'Jl. Ahmad Yani No. 404, Martapura, Kalimantan Selatan',
                'telepon' => '08901234567',
                'email' => 'martapura.courier@example.com',
            ],
            [
                'nama' => 'Banjarbaru Transport',
                'jenis' => 'pickup',
                'alamat' => 'Jl. Trikora No. 505, Banjarbaru, Kalimantan Selatan',
                'telepon' => '09012345678',
                'email' => 'banjarbaru.transport@example.com',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create(array_merge($vendor, [
                'status' => 'aktif', // Set default status to 'aktif'
            ]));
        }
    }
}
