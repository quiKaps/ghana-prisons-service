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
        Schema::create('inter_station_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained('inmates')->onDelete('cascade');
            $table->foreignId('from_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('to_station_id')->constrained('stations')->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->string('officer_in_charge')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
            $table->string('transfer_order_number')->unique()->nullable();
            $table->string('transfer_document')->nullable(); // Path to the transfer document
            $table->string('remarks')->nullable();
            $table->string('created_by')->nullable(); // User who created the transfer
            $table->string('updated_by')->nullable(); // User who last updated the transfer
            $table->string('approved_by')->nullable(); // User who approved the transfer
            $table->dateTime('approved_at')->nullable(); // Timestamp when the transfer was approved
            $table->dateTime('completed_at')->nullable(); // Timestamp when the transfer was completed
            $table->dateTime('cancelled_at')->nullable(); // Timestamp when the transfer was cancelled
            $table->string('cancellation_reason')->nullable(); // Reason for cancellation, if applicable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter_station_transfers');
    }
};
