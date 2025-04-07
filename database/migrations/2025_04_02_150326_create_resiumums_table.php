<?php

use App\Enums\JenisLayanan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Events\DiscoverEvents;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resiumums', function (Blueprint $table) {
            //Kota asal-tujuan
            $table->string('ty_cust')->index(); // umum, langganan
            $table->unsignedMediumInteger('nama_customer')->unsigned()->nullable();
            $table->unsignedSmallInteger('orig_regency_id')->index();
            $table->unsignedTinyInteger('province_id')->index();
            $table->unsignedSmallInteger('regency_id')->index();
            $table->unsignedMediumInteger('district_id')->index();
            $table->string('etd')->nullable()->index();
            $table->string('layanan_id', 50); // regular, express, etc
            //Dimensi Barang dan berat barang
            $table->json('dimensi')->nullable();
            $table->unsignedMediumInteger('weight'); // in KG normal
            $table->unsignedMediumInteger('koli'); // in Koli
            $table->unsignedMediumInteger('pcs_count'); //dus
            $table->text('items_detail')->nullable();
            $table->float('total_volume')->default(0);
            $table->float('total_weight_volume')->default(0);
            $table->string('status');

            //$table->tinyInteger('p')->default(0);
            // $table->tinyInteger('l')->default(0);
            // $table->tinyInteger('t')->default(0);
            //  // $table->decimal('totalVolume', 10, 3)->nullable();
            //  $table->decimal('totalWeightVolume', 10, 2)->nullable();

            $table->enum('charged_on', ['Paket', 'Dokumen', 'Kubikasi'])->index(); // Assuming these are the options; //biaya berdasarkan
            $table->boolean('jenis_kiriman')->index()->unsigned()->default(1)->comment('1:Paket, 2:Dokumen'); // jenis kiriman
            $table->unsignedMediumInteger('discount'); // in ID
            //biaya admin
            $table->string('biaya_admin')->nullable()->default(2)->comment('1:ada, 2:tidak ada');
            //insuransi
            $table->string('insurance')->nullable()->default(1)->comment('1:ada, 2:tidak ada');
            $table->decimal('insurance_value', 12, 2)->nullable(); // in IDR Nilai paket
            $table->unsignedMediumInteger('biaya_packing')->default(0);
            $table->unsignedMediumInteger('biaya_tambahan')->default(0)->index();

            //Perhitungan ongkir

            $table->decimal('tariff', 12, 2)->nullable()->index();
            $table->decimal('ongkir', 12, 2)->nullable()->index();
            $table->decimal('total', 12, 2)->nullable()->index();


            //data resi
            $table->increments('id');
            $table->string('noresi');
            $table->char('nofakt')->nullable();
            $table->date('date_input');

            $table->string('jenis_pembayaran')->nullable(); //tunai,kredit,cod
            $table->string('satuan_barang')->nullable();
            $table->string('kurir_pickup')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('desk_brg')->nullable(); // diskripsi barang
            $table->string('catatan')->nullable();

            //data pengirim-pengirim
            $table->string('nama_pengirim');
            $table->string('alamat_pengirim');
            $table->string('telp_pengirim');
            $table->string('nama_penerima');
            $table->string('alamat_penerima');
            $table->string('telp_penerima');
            $table->string('kode_pos');
            $table->string('kode_pos_asal');
            $table->unsignedBigInteger('last_officer_id')->nullable();
            $table->string('last_location_id', 7)->nullable();
            $table->unsignedInteger('pickup_courier_id')->nullable();
            $table->unsignedInteger('delivery_courier_id')->nullable();
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('last_officer_id')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resiumums');
    }
};
