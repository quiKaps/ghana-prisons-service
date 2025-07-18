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
        Schema::create('inter_cell_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained('inmates')->onDelete('cascade');
            $table->foreignId('from_cell_id')->constrained('cells')->onDelete('cascade');
            $table->foreignId('to_cell_id')->constrained('cells')->onDelete('cascade');
            $table->date('transfer_date');
            $table->string('reason')->nullable();
            $table->string('officer_in_charge')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter_cell_transfers');
    }
};
