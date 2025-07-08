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

        Schema::create('inmates', function (Blueprint $table) {
            $table->id();

            // Identity & Admission
            $table->string('prisoner_picture')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('full_name')->index();
            $table->enum('gender', ['male', 'female']);
            $table->enum('married_status', ['single', 'married', 'divorced', 'widowed', 'separated'])->nullable();
            $table->integer('age_on_admission')->unsigned();
            $table->date('admission_date');
            $table->boolean('previously_convicted')->default(false);
            $table->string('previous_sentence')->nullable();
            $table->string('previous_offence')->nullable();
            $table->string('previous_station_id')->nullable();

            // Location
            $table->foreignId('station_id')->nullable()->constrained('stations')->nullOnDelete()->index();
            $table->foreignId('cell_id')->nullable()->constrained('cells')->nullOnDelete()->index();
            $table->string('court_of_committal')->nullable();

            // Next of kin
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_contact')->nullable();

            // Personal details
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('education_level')->nullable();
            $table->string('occupation')->nullable();
            $table->string('hometown')->nullable();
            $table->string('tribe')->nullable();

            // Physical characteristics
            $table->json('distinctive_marks')->nullable();

            $table->string('part_of_the_body')->nullable();

            // Languages
            $table->json('languages_spoken')->nullable();

            // Disability
            $table->boolean('disability')->nullable();
            $table->json('disability_type')->nullable();

            // Police details
            $table->string('police_name')->nullable();
            $table->string('police_station')->nullable();
            $table->string('police_contact')->nullable();

            // Goaler
            $table->boolean('goaler')->nullable();
            $table->json('goaler_document')->nullable();

            // Transfer details
            $table->boolean('transferred_in')->nullable();
            $table->foreignId('station_transferred_from_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->date('date_transferred_in')->nullable();
            $table->boolean('transferred_out')->nullable()->index();
            $table->foreignId('station_transferred_to_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->date('date_transferred_out')->nullable();

            // Previous convictions
            $table->json('previous_convictions')->nullable();

            // Discharge
            $table->string('mode_of_discharge')->nullable();
            $table->boolean('is_discharged')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmates');
    }
};
