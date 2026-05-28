<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('teachers', 'department')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('teachers', 'department')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('department')->nullable()->after('contactno');
            });
        }
    }
};
