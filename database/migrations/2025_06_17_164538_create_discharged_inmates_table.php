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
        Schema::create('discharged_inmates', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->enum('inmate_type', ['convict', 'remand', 'trial']);
            $table->string('full_name'); // For remand/trial
            $table->string('country_of_origin');
            $table->string('offense')->nullable(); // Shared but named consistently
            $table->date('admission_date')->nullable();
            $table->integer('age_on_admission')->nullable();
            $table->string('court')->nullable(); // Shared for both, or use court_of_committal if needed
            $table->string('sentence')->nullable();         // convict only
            $table->date('date_sentenced')->nullable();     // convict only
            $table->date('next_court_date')->nullable();    // remand/trial only
            $table->enum('detention_type', ['remand', 'trial'])->nullable(); // remand/trial only
            $table->foreignId('station_id')->nullable()->constrained()->onDelete('set null');
            $table->string('warrant')->nullable();
            $table->string('warrant_document')->nullable();
            $table->string('photo')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('signature')->nullable();
            $table->string('police_name')->nullable();
            $table->string('police_station')->nullable();
            $table->string('police_contact')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_contact')->nullable();
            $table->string('mode_of_discharge');
            $table->string('date_of_discharge');
            $table->string('discharged_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharged_inmates');
    }
};
