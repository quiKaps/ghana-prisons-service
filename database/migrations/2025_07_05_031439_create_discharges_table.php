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
        Schema::create('discharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')
                ->constrained()->onDelete('cascade');
            $table->string('discharge_type'); // e.g., parole, completion of sentence, medical discharge
            $table->date('discharge_date');
            $table->string('reason')->nullable(); // Reason for discharge, if applicable
            $table->string('discharge_document')->nullable(); // Document related to discharge, if applicable
            $table->foreignId('discharged_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Officer responsible for discharge
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharges');
    }
};
