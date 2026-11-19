<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('status', 20)->default('new')->after('consent')->index();
            $table->text('notes')->nullable()->after('status');
            $table->foreignId('assigned_to')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->string('page_slug', 160)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn(['status', 'notes', 'page_slug']);
        });
    }
};
