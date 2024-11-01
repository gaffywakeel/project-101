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
        Schema::create('commerce_fees', function (Blueprint $table) {
            $table->id();

            $table->unsignedDecimal('value', 36, 18);

            $table->foreignId('wallet_id')->unique()
                ->constrained('wallets')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commerce_fees');
    }
};
