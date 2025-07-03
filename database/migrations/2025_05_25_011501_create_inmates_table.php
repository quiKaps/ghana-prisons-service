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
            $table->string('prisoner_picture')->nullable();
            $table->string('serial_number')
                ->nullable()
                ->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->enum('married_status', ['single', 'married', 'divorced', 'widowed', 'separated'])->nullable();
            $table->integer('age_on_admission')->unsigned();
            //$table->date('date_of_birth')->nullable();
            $table->longText('offence');
            $table->string('sentence');
            $table->date('admission_date');
            $table->date('date_sentenced');
            $table->boolean('previously_convicted')->default(false);
            $table->string('previous_sentence')->nullable();
            $table->string('previous_offence')->nullable();
            $table->string('previous_station_id')->nullable();
            $table->foreignId('station_id')->nullable()->constrained('stations');
            $table->foreignId('cell_id')->nullable()->constrained('cells');
            $table->string('court_of_committal')->nullable();
            $table->date('EPD')
                ->nullable();
            $table->date('LPD')
                ->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_contact')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('education_level')->nullable();
            $table->string('occupation')->nullable();
            $table->string('hometown')->nullable();
            $table->string('tribe')->nullable();
            $table->json('distinctive_marks')->nullable();
            $table->string('part_of_the_body')->nullable();
            $table->json('languages_spoken')->nullable();
            $table->boolean('disability')->nullable();
            $table->string('police_name')->nullable();
            $table->string('police_station')->nullable();
            $table->string('police_contact')->nullable();
            $table->boolean('goaler')->nullable();
            $table->boolean('transferred_in')->nullable();
            $table->integer('station_transferred_from_id')->nullable();
            $table->date('date_transferred_in')->nullable();
            $table->boolean('transferred_out')->nullable();
            $table->integer('station_transferred_to_id')->nullable();
            $table->date('date_transferred_out')->nullable();
            $table->string('warrant_document')->nullable();
            $table->json('disability_type')->nullable();
            $table->json('goaler_document')->nullable();
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
