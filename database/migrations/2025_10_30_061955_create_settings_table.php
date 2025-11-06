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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('backup_time')
                  ->default(env('BACKUP_TIME'));
            $table->boolean('google_drive_backup')
                  ->default(false);
            $table->boolean('dropbox_backup')
                  ->default(false);
            $table->boolean('s3_backup')
                  ->default(false);
            $table->string('google_drive_client_id')->default(env('GOOGLE_DRIVE_CLIENT_ID'))
                ->nullable();
            $table->string('google_drive_client_secret')->default(env('GOOGLE_DRIVE_CLIENT_SECRET'))
                ->nullable();
            $table->string('google_drive_refresh_token')->default(env('GOOGLE_DRIVE_REFRESH_TOKEN'))
                ->nullable();
            $table->string('google_drive_folder')->default(env('GOOGLE_DRIVE_FOLDER'))
                ->nullable();
            $table->string('dropbox_authorization_token')->default(env('DROPBOX_AUTH_TOKEN'))
                ->nullable();
            $table->string('dropbox_folder')->default(env('DROPBOX_FOLDER'))
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
