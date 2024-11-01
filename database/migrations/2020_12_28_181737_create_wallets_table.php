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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->integer('min_conf')->default(3);
            $table->text('passphrase');
            $table->text('resource');
            $table->unsignedBigInteger('coin_id')->unique();
            $table->foreign('coin_id')->references('id')
                ->on('coins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
