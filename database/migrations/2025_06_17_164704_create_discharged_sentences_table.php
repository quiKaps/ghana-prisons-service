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
        Schema::create('discharged_sentences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discharged_inmate_id')->constrained('discharged_inmates')->onDelete('cascade');

            $table->string('offense');
            $table->string('sentence');
            $table->date('admission_date');
            $table->date('date_sentenced')->nullable();
            $table->string('court_of_committal');
            $table->date('EPD')->nullable();
            $table->date('LPD'); // required — as you emphasized

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharged_sentences');
    }
};
