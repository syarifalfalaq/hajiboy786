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
        Schema::create('vendor_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('origin_city');
            $table->string('province_id');
            $table->string('regency_id');
            $table->string('destination_district');
            $table->decimal('harga', 12, 2);
            $table->enum('jenis_pengiriman', ['reguler', 'express'])->default('reguler');
            $table->decimal('berat_minimum', 8, 2)->default(1.00);
            $table->decimal('harga_per_kg_tambahan', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_rates');
    }
};
