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
        Schema::table('wallet_addresses', function (Blueprint $table) {
            $table->boolean('consolidated')->default(true)->after('label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_addresses', function (Blueprint $table) {
            $table->dropColumn('consolidated');
        });
    }
};
