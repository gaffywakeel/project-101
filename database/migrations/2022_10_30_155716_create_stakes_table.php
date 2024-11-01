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
        Schema::create('stakes', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['holding', 'pending', 'redeemed'])->default('holding');

            $table->decimal('value', 36, 0)->unsigned();
            $table->decimal('yield', 36, 0)->unsigned();

            $table->decimal('days')->unsigned();
            $table->decimal('rate')->unsigned();

            $table->dateTime('redemption_date');

            $table->foreignId('wallet_account_id')
                ->constrained('wallet_accounts')->cascadeOnDelete();

            $table->foreignId('plan_id')->nullable()
                ->constrained('stake_plans')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stakes');
    }
};
