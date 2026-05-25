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
        Schema::create('alternative_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete()
                ->comment('Assessment tempat nilai ini dicatat');

            $table->foreignId('alternative_id')
                ->constrained('alternatives')
                ->cascadeOnDelete()
                ->comment('Alternatif yang dinilai');

            $table->foreignId('criteria_id')
                ->constrained('criteria')
                ->cascadeOnDelete()
                ->comment('Kriteria yang digunakan dalam penilaian');

            $table->decimal('value', 10, 4)->comment('Nilai alternatif untuk kriteria ini. Bisa berupa skor atau nilai numerik lain sesuai jenis kriteria');

            $table->foreignId('input_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Siapa yang menginput nilai ini.');

            $table->timestamps();

            $table->unique(['assessment_id', 'alternative_id', 'criteria_id'], 'uq_assessment_alternative_criteria');

            $table->index('assessment_id');
            $table->index('input_by');
            $table->index(['assessment_id', 'criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternative_values');
    }
};
