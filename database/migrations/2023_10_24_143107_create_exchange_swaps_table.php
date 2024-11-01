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
        Schema::create('exchange_swaps', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['completed', 'pending', 'canceled'])->default('pending');

            $table->decimal('buy_value', 36, 0)->unsigned();
            $table->decimal('buy_dollar_price', 36, 18)->unsigned();

            $table->foreignId('buy_wallet_account_id')
                ->constrained('wallet_accounts')->cascadeOnDelete();

            $table->decimal('sell_value', 36, 0)->unsigned();
            $table->decimal('sell_dollar_price', 36, 18)->unsigned();

            $table->foreignId('sell_wallet_account_id')
                ->constrained('wallet_accounts')->cascadeOnDelete();

            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_swaps');
    }
};
