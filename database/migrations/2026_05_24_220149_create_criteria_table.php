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
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->comment('Nama kriteria, contoh: Efektivitas Mitigasi');
            $table->text('description')->nullable()->comment('Penjelasan kriteria dan cara penilaiannya');
            $table->enum('type', ['benefit', 'cost'])->comment('Tipe kriteria, benefit berarti semakin tinggi nilainya semakin baik, cost berarti semakin rendah nilainya semakin baik');
            $table->decimal('weight', 5, 4)->default(0.0000)->comment('Bobot kriteria (0.0000 - 1.0000). Total bobot ideal adalah 1.0000');

            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
