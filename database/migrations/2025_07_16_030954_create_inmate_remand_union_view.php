<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        CREATE OR REPLACE VIEW inmate_remand_union AS
SELECT
    ROW_NUMBER() OVER () AS id,
    id AS unique_id,
    'inmate' AS source,
    station_id,
    full_name,
    gender,
    age_on_admission,
    admission_date,
    serial_number,
    court_of_committal AS court,
    is_discharged,
    'convict' AS detention_type
FROM inmates

UNION ALL

SELECT
    ROW_NUMBER() OVER () AS id,
    id AS unique_id,
    'remandtrial' AS source,
    station_id,
    full_name,
    gender,
    age_on_admission,
    admission_date,
    serial_number,
    court,
    is_discharged,
    detention_type
FROM remand_trials;
");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS inmate_remand_union;");
    }
};
