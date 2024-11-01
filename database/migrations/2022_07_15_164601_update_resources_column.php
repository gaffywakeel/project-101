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
        Schema::table('wallets', function (Blueprint $table) {
            $table->longText('resource')->change();
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->longText('resource')->change();
        });

        Schema::table('wallet_addresses', function (Blueprint $table) {
            $table->longText('resource')->change();
        });
    }
};
