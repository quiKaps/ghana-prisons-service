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
            $table->string('offence');
            $table->date('EPD');
            $table->date('LPD');
            $table->string('goaler_document')->nullable();
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
