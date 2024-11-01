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
        Schema::create('withdrawal_fees', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['percent', 'fixed']);
            $table->unsignedDouble('value')->nullable();

            $table->unsignedBigInteger('wallet_id')->unique();
            $table->foreign('wallet_id')->references('id')
                ->on('wallets')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_fees');
    }
};
