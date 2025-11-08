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
         Schema::table('inmates', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['cell_id']);

            // Drop the old foreign key column (optional, if you want to replace it)
            $table->dropColumn('cell_id');

            // Add the new string-based cell_id column
            $table->string('cell_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
