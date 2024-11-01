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
        Schema::table('transfer_records', function (Blueprint $table) {
            $table->decimal('dollar_price', 36, 18)->unsigned()->change();
        });

        Schema::table('exchange_trades', function (Blueprint $table) {
            $table->decimal('dollar_price', 36, 18)->unsigned()->change();
        });
    }
};
