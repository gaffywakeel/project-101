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
        Schema::table('giftcards', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giftcards', function (Blueprint $table) {
            $table->decimal('value', 18, 0)->unsigned()->change();
        });
    }
};
