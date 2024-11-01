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
        Schema::create('commerce_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();

            $table->string('email');

            $table->foreignId('commerce_account_id')
                ->constrained('commerce_accounts')->cascadeOnDelete();

            $table->unique(['email', 'commerce_account_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commerce_customers');
    }
};
