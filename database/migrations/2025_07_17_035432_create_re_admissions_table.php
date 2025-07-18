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
        Schema::create('re_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')
                ->constrained()->onDelete('cascade');
            $table->foreignId('remand_trial_id')->constrained()->cascadeOnDelete();
            $table->date('re_admission_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_admissions');
    }
};
