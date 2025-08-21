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
        Schema::create('remand_trial_discharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')
                ->constrained()->onDelete('cascade');
            $table->foreignId('remand_trial_id')->constrained()->cascadeOnDelete();
            $table->string('prisoner_type');
            $table->date('discharge_date');
            $table->string('mode_of_discharge')->nullable();
            $table->string('discharged_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remand_trial_discharges');
    }
};
