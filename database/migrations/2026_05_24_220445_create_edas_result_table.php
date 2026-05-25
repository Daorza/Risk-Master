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
        Schema::create('edas_result', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete()
                ->comment('Assessment yang menghasilkan baris ini');

            $table->foreignId('alternative_id')
                ->constrained('alternatives')
                ->cascadeOnDelete()
                ->comment('Alternatif yang dievaluasi');

            // Menghitung nilai EDAS

            $table->decimal('pda', 10, 6)->default(0.000000)->comment('Positive Distance from Average — jarak positif terbobot dari AV');
            $table->decimal('nda', 10, 6)->default(0.000000)->comment('Negative Distance from Average — jarak negatif terbobot dari AV');
            $table->decimal('sp', 10, 6)->default(0.000000)->comment('Penjumlahan terbobot dari PDA (SP = total(w * PDA))');
            $table->decimal('sn', 10, 6)->default(0.000000)->comment('Penjumlahan terbobot dari NDA (SN = total(w * NDA))');

            // Hitung normalisasi

            $table->decimal('nsp', 8, 6)->default(0.000000)->comment('Normalized SP (NSP = SP / max(SP), range 0-1)');
            $table->decimal('nsn', 8, 6)->default(0.000000)->comment('Normalized SN (NSN = 1 - (SN / max(SN)), range 0-1)');

            // skor akhir AS & rAnking

            $table->decimal('appraisal_score', 8, 6)->default(0.000000)->comment('Appraisal Score (AS = (NSP + NSN) / 2, range 0-1)');
            $table->unsignedSmallInteger('rank')->comment('Peringkat alternatif (1 = terbaik), urut berdasarkan appraisal_score dari yang tertinggi ke terendah');

            $table->timestamps('calculated_at')->useCurrent()->comment('Waktu kalkulasi EDAS dijalankan');

            $table->unique(['assessment_id', 'alternative_id'], 'uq_edas_assessment_alternative');

            $table->index(['assessment_id', 'rank']);
            $table->index('assessment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edas_result');
    }
};
