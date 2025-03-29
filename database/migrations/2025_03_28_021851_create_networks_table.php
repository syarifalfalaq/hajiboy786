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
        Schema::create('networks', function (Blueprint $table) {
            $table->id();

            $table->boolean('type_id')->unsigned()->index(); // Tipe jaringan (1 = Cabang, 2 = Kantor Cabang, 3 =gerai)
            $table->char('code', 8)->unique(); //kode wilayah cabang/kabupaten
            $table->string('name', 60); // nama kantorcaBang
            $table->string('address')->nullable();
            $table->string('kordinat', 60)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 60)->nullable();
            $table->unsignedTinyInteger('orig_province_id');
            $table->unsignedSmallInteger('orig_regency_id');
            $table->unsignedMediumInteger('orig_district_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
