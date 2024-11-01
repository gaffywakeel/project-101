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
        Schema::create('commerce_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->enum('type', ['single', 'multiple']);

            $table->boolean('status')->default(true);

            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 18, 0)->unsigned();
            $table->string('currency');

            $table->string('redirect')->nullable();
            $table->text('message')->nullable();

            $table->dateTime('expires_at')->nullable();

            $table->enum('source', ['web', 'api']);

            $table->foreignId('commerce_account_id')
                ->constrained('commerce_accounts')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commerce_payments');
    }
};
