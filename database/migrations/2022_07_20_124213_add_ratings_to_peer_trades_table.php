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
        Schema::table('peer_trades', function (Blueprint $table) {
            $table->foreignId('seller_rating_id')->nullable()
                ->constrained('ratings')->nullOnDelete();

            $table->foreignId('buyer_rating_id')->nullable()
                ->constrained('ratings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peer_trades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_rating_id');
            $table->dropConstrainedForeignId('buyer_rating_id');
        });
    }
};
