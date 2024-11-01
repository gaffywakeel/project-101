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
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable();
            $table->dropUnique(['name']);
            $table->unique(['name', 'guard_name']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->renameColumn('rank', 'order');
            $table->dropUnique(['name']);
            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            $table->unique('name');
            $table->dropColumn(['order']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            $table->unique('name');
            $table->renameColumn('order', 'rank');
        });
    }
};
