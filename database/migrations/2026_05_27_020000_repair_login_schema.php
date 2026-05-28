<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_account')) {
            Schema::create('user_account', function (Blueprint $table) {
                $table->id();
                $table->string('username')->unique();
                $table->string('email')->nullable();
                $table->string('password');
                $table->string('role')->default('student');
                $table->boolean('is_active')->default(true);
                $table->boolean('must_change_password')->default(false);
                $table->boolean('is_first_login')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('students') && ! Schema::hasColumn('students', 'user_account_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('user_account_id')->nullable()->constrained('user_account');
            });
        }

        if (Schema::hasTable('teachers') && ! Schema::hasColumn('teachers', 'user_account_id')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->foreignId('user_account_id')->nullable()->constrained('user_account')->nullOnDelete();
            });
        }

        $admin = DB::table('user_account')->where('username', 'admin')->first();

        if ($admin) {
            DB::table('user_account')->where('username', 'admin')->update([
                'email' => 'admin@example.com',
                'role' => 'admin',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => false,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('user_account')->insert([
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
};
