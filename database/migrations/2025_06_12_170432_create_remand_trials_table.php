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
        Schema::create('remand_trials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cell_id')->nullable()->constrained()->onDelete('set null');
            $table->string('serial_number')->unique();
            $table->string('full_name');
            $table->string('offense');
            $table->date('admission_date');
            $table->integer('age_on_admission')->nullable();
            $table->string('court');
            $table->enum('detention_type', ['remand', 'trial']);
            $table->date('next_court_date');
            $table->string('warrant')->nullable();
            $table->string('country_of_origin');
            $table->string('police_station')->nullable();
            $table->string('police_officer')->nullable();
            $table->string('police_contact')->nullable();
            $table->date('re_admission_date')->nullable();
            $table->string('picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remand_trials');
    }
};
