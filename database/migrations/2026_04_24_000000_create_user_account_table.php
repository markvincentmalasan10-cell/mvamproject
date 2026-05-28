<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_account_id')->nullable()->after('degree_id')->constrained('user_account');
        });

        $students = DB::table('students')
            ->select('id', 'email', 'username', 'password', 'is_first_login')
            ->whereNotNull('username')
            ->get();

        foreach ($students as $student) {
            $userAccountId = DB::table('user_account')->insertGetId([
                'username' => $student->username,
                'email' => $student->email,
                'password' => $student->password,
                'role' => 'student',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => (bool) $student->is_first_login,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('students')
                ->where('id', $student->id)
                ->update(['user_account_id' => $userAccountId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_account_id');
        });

        Schema::dropIfExists('user_account');
    }
};
