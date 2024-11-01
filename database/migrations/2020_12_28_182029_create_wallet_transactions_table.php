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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->dateTime('date');
            $table->enum('type', ['send', 'receive']);

            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')->references('id')
                ->on('wallets')->onDelete('cascade');

            $table->decimal('value', 36, 0)->unsigned();

            $table->integer('confirmations')->default(0);
            $table->text('resource');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
