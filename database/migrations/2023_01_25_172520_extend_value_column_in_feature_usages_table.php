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
        Schema::table('feature_usages', function (Blueprint $table) {
            $table->decimal('value', 36, 18)->unsigned()->change();
        });
    }
};
