<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->enum('customer_id', ['Umum', 'Langganan'])->default('Umum');
            $table->enum('service_id', ['Reguler', 'Express'])->default('Reguler');
            $table->enum('pack_type_id', ['Paket', 'Dokumen', 'Kubikasi'])->default('Paket');
            $table->unsignedMediumInteger('nama_customer')->unsigned()->nullable();

            $table->unsignedSmallInteger('orig_regency_id');
            $table->unsignedSmallInteger('regency_id');
            $table->unsignedMediumInteger('district_id');
            $table->unsignedSmallInteger('province_id');
            $table->unsignedInteger('rate_kg')->nullable();
            $table->unsignedInteger('rate_pc')->nullable();
            $table->unsignedInteger('rate_koli')->nullable();
            $table->tinyInteger('min_weight')->unsigned()->default(1);

            $table->string('etd', 10);
            $table->tinyInteger('discount')->unsigned()->nullable();
            $table->unsignedInteger('add_cost')->nullable();
            $table->string('notes')->nullable();
            $table->foreign('orig_regency_id')->references('id')->on('regencies')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

            // Add indexes
            $table->index('orig_regency_id');
            $table->index('regency_id');
            $table->index('district_id');
            $table->index('province_id');
            $table->index('rate_kg');
            $table->index('rate_pc');
            $table->index('rate_koli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
