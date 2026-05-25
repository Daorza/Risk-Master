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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Siapa pemilik assessment ini.');

            $table->string('title', 200)->comment('Judul assessment, contoh: Analisis Risiko Jaringan Q3 2026');
            $table->text('description')->nullable()->comment('Konteks dan latar belakang assessment');
            $table->enum('status', ['draft', 'completed'])
                ->default('draft')
                ->comment('Status assessment. Draft = belum selesai, Completed = EDAS sudah dikalkulasikan');

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
