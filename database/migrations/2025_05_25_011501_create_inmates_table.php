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
            $table->string('serial_number')->unique();
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('married_status', ['single', 'married', 'divorced', 'widowed']);
            $table->integer('age_on_admission')->unsigned();
            $table->date('date_of_birth')->nullable();
            $table->longText('offence');
            $table->string('sentence');
            $table->date('admission_date');
            $table->date('date_sentenced');
            $table->boolean('previously_convicted')->default(false);
            // $table->foreignId('previous_conviction_id')->nullable()->constrained('previous_convictions')->onDelete('set null');
            $table->foreignId('station_id')->nullable()->constrained('stations')->onDelete('set null');
            $table->foreignId('cell_id')->nullable()->constrained('cells')->onDelete('set null');
            $table->string('court_of_committal')->nullable();
            $table->date('EPD');
            $table->date('LPD');
            $table->string('photo')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('signature')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_contact')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->json('allergies')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('education_level')->nullable();
            $table->string('occupation')->nullable();
            $table->string('hometown')->nullable();
            $table->string('tribe')->nullable();
            $table->string('distinctive_marks')->nullable();
            $table->json('languages_spoken')->nullable();
            $table->boolean('disability')->nullable();
            $table->string('disability_type')->nullable();
            $table->string('police_name')->nullable();
            $table->string('police_station')->nullable();
            $table->string('police_contact')->nullable();
            $table->boolean('goaler')->nullable();
            $table->string('goaler_document')->nullable();
            $table->string('warrant_document')->nullable();
            //transferedin
            //transferedout
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
