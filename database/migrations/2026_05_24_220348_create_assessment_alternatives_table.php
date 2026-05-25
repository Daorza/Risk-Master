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
        Schema::create('assessment_alternatives', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete()
                ->comment('Assessment yang menyertakan alternatif ini');

            $table->foreignId('alternative_id')
                ->constrained('alternatives')
                ->cascadeOnDelete()
                ->comment('Alternatif yang diikutsertakan');

            $table->unique(['assessment_id', 'alternative_id'], 'uq_assessment_alternative');

            $table->index('assessment_id');
            $table->index('alternative_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_alternatives');
    }
};
