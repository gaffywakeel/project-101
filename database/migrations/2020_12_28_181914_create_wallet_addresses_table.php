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
        Schema::create('wallet_addresses', function (Blueprint $table) {
            $table->string('address')->primary();
            $table->string('label')->nullable();

            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')->references('id')
                ->on('wallets')->onDelete('cascade');

            $table->unsignedBigInteger('wallet_account_id');
            $table->foreign('wallet_account_id')->references('id')
                ->on('wallet_accounts')->onDelete('cascade');

            $table->text('resource');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_addresses');
    }
};
