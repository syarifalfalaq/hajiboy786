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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('sektor_usaha_id');

            $table->char('id_customer', 18);
            $table->string('kode', 20)->nullable();
            $table->string('nama', 60);
            $table->string('npwp', 30)->nullable();
            $table->boolean('is_taxed')->unsigned();
            $table->boolean('is_active')->unsigned()->default(1)->index();
            $table->text('detail_pic');
            $table->date('start_date');
            $table->string('address');
            $table->unsignedBigInteger('network_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();

            $table->unsignedBigInteger('regency_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            //  $table->unsignedBigInteger('village_id');
            $table->unsignedTinyInteger('category_id')->default(1)->comment('Available category: 1, 2, 3')->index();
            $table->timestamps();


            $table->foreign('sektor_usaha_id')->references('id')->on('sektor_usahas')->onDelete('cascade');

            $table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
