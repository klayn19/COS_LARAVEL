<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL enum() creates CHECK constraints (e.g. questions_type_check, student_scores_type_check)
        // that block inserting 'assessment' and 'prototype'. We drop them so all types work on Render.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_type_check");
            DB::statement("ALTER TABLE student_scores DROP CONSTRAINT IF EXISTS student_scores_type_check");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE questions ADD CONSTRAINT questions_type_check CHECK (type IN ('quiz', 'exam', 'assessment', 'prototype'))");
            DB::statement("ALTER TABLE student_scores ADD CONSTRAINT student_scores_type_check CHECK (type IN ('quiz', 'exam', 'assessment', 'prototype'))");
        }
    }
};
