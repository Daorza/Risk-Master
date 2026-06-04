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
        Schema::table('assessments', function (Blueprint $table) {
            $table->longText('title')->change();
            $table->longText('description')->nullable()->change();
            $table->string('title_hash', 64)->nullable()->after('title')->index();
        });

        Schema::table('alternatives', function (Blueprint $table) {
            $table->longText('name')->change();
            $table->longText('description')->nullable()->change();
            $table->string('name_hash', 64)->nullable()->after('name')->index();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->longText('old_data')->nullable()->change();
            $table->longText('new_data')->nullable()->change();
            $table->string('ip_address', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('title', 200)->change();
            $table->text('description')->nullable()->change();
            $table->dropColumn('title_hash');
        });

        Schema::table('alternatives', function (Blueprint $table) {
            $table->string('name', 150)->change();
            $table->text('description')->nullable()->change();
            $table->dropColumn('name_hash');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->json('old_data')->nullable()->change();
            $table->json('new_data')->nullable()->change();
            $table->string('ip_address', 45)->nullable()->change();
        });
    }
};
