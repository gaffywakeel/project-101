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
        Schema::create('operating_country_bank', function (Blueprint $table) {
            $table->id();

            $table->string('operating_country_code');
            $table->foreign('operating_country_code')->references('code')
                ->on('operating_countries')->onDelete('cascade');

            $table->bigInteger('bank_id')->unsigned();
            $table->foreign('bank_id')->references('id')
                ->on('banks')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_country_bank');
    }
};
