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
        Schema::create('sentences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')
                ->nullable()
                ->onDelete('cascade');
            $table->string('sentence');
            $table->string('total_sentence')->nullable(); // Total sentence duration, e.g., '5 years'
            $table->string('reduced_sentence')->nullable(); // Officer who commuted the
            $table->string('offence');
            $table->date('EPD')->nullable();
            $table->date('LPD')->nullable();
            $table->string('court_of_committal')->nullable();
            $table->string('commutted_by')->nullable();
            $table->string('commutted_sentence')->nullable();
            // $table->string('goaler_document')->nullable();
            $table->date('date_of_amnesty')->nullable();
            $table->string('amnesty_document')->nullable();
            $table->string('warrant_document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentences');
    }
};
