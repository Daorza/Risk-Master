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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Siapa yang melakukan aksi ini. Null jika aksi dilakukan oleh sistem.');

            $table->string('action', 50)->comment('Jenis aksi: created, updated, deleted, login, logout, calculate_edas, dsb.');
            $table->string('table_name', 100)->comment('Nama tabel yang terdampak, contoh: assessments, alternatives, criteria, dsb.');
            $table->unsignedBigInteger('record_id')->nullable()->comment('ID record yang terdampak di tabel tersebut');

            $table->json('old_data')->nullable()->comment('Data sebelum perubahan (null untuk created)');
            $table->json('new_data')->nullable()->comment('Data setelah perubahan (null untuk deleted)');

            $table->string('ip_address', 45)->nullable()->comment('IP address client. 45 untuk IPv6 support');

            $table->timestamps('created_at')->useCurrent()->comment('Waktu aksi dilakukan');

            $table->index('user_id');
            $table->index('table_name');
            $table->index(['table_name', 'record_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
