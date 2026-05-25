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
        Schema::create('alternatives', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->comment('Nama alternatif, contoh: Web Application Firewall (WAF)');
            $table->text('description')->nullable()->comment('Penjelasan alternatif, cara implementasi, dan konteks penggunaannya');
            $table->enum('source', ['admin', 'user'])->default('admin')->comment('Sumber alternatif. Admin = template dari admin, User = alternatif diinput mandiri oleh user');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Siapa yang membuat alternatif ini.');

            $table->index('source');
            $table->index('creaded_by');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternatives');
    }
};
