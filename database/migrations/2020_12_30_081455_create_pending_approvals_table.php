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
        Schema::create('pending_approvals', function (Blueprint $table) {
            $table->id();

            $table->string('ref');
            $table->string('state');
            $table->string('hash')->nullable();

            $table->unsignedBigInteger('transfer_record_id')->unique()->nullable();
            $table->foreign('transfer_record_id')->references('id')
                ->on('transfer_records')->onDelete('cascade');

            $table->longText('resource');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_approvals');
    }
};
