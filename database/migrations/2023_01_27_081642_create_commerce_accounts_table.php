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
        Schema::create('commerce_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('website')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->text('about')->nullable();

            $table->string('logo')->nullable();

            $table->foreignId('user_id')->unique()
                ->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commerce_accounts');
    }
};
