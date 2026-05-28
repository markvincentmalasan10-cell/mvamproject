<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RepairSchema extends Command
{
    protected $signature = 'app:repair-schema';

    protected $description = 'Repair required CRUD tables and columns without relying on migration status.';

    public function handle(): int
    {
        $this->ensureUserAccountTable();
        $this->ensureDegreesTable();
        $this->ensureStudentsTable();
        $this->ensureTeachersTable();
        $this->ensureCoursesTables();

        $this->info('Required application schema is present.');

        return self::SUCCESS;
    }

    private function ensureUserAccountTable(): void
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

            return;
        }

        $this->addColumns('user_account', [
            'username' => fn (Blueprint $table) => $table->string('username')->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'password' => fn (Blueprint $table) => $table->string('password')->nullable(),
            'role' => fn (Blueprint $table) => $table->string('role')->default('student'),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
            'must_change_password' => fn (Blueprint $table) => $table->boolean('must_change_password')->default(false),
            'is_first_login' => fn (Blueprint $table) => $table->boolean('is_first_login')->default(true),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function ensureDegreesTable(): void
    {
        if (! Schema::hasTable('degrees')) {
            Schema::create('degrees', function (Blueprint $table) {
                $table->id();
                $table->string('degree_title')->nullable();
                $table->timestamps();
            });

            return;
        }

        $this->addColumns('degrees', [
            'degree_title' => fn (Blueprint $table) => $table->string('degree_title')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function ensureStudentsTable(): void
    {
        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('fname')->nullable();
                $table->string('mname')->nullable();
                $table->string('lname')->nullable();
                $table->string('email')->nullable();
                $table->string('contactno')->nullable();
                $table->string('degree_title')->nullable();
                $table->unsignedBigInteger('degree_id')->nullable();
                $table->unsignedBigInteger('user_account_id')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();
            });

            return;
        }

        $this->addColumns('students', [
            'fname' => fn (Blueprint $table) => $table->string('fname')->nullable(),
            'mname' => fn (Blueprint $table) => $table->string('mname')->nullable(),
            'lname' => fn (Blueprint $table) => $table->string('lname')->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'contactno' => fn (Blueprint $table) => $table->string('contactno')->nullable(),
            'degree_title' => fn (Blueprint $table) => $table->string('degree_title')->nullable(),
            'degree_id' => fn (Blueprint $table) => $table->unsignedBigInteger('degree_id')->nullable(),
            'user_account_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_account_id')->nullable(),
            'image_path' => fn (Blueprint $table) => $table->string('image_path')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function ensureTeachersTable(): void
    {
        if (! Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('fname')->nullable();
                $table->string('mname')->nullable();
                $table->string('lname')->nullable();
                $table->string('email')->nullable();
                $table->string('contactno')->nullable();
                $table->unsignedBigInteger('user_account_id')->nullable();
                $table->timestamps();
            });

            return;
        }

        $this->addColumns('teachers', [
            'fname' => fn (Blueprint $table) => $table->string('fname')->nullable(),
            'mname' => fn (Blueprint $table) => $table->string('mname')->nullable(),
            'lname' => fn (Blueprint $table) => $table->string('lname')->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'contactno' => fn (Blueprint $table) => $table->string('contactno')->nullable(),
            'user_account_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_account_id')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function ensureCoursesTables(): void
    {
        if (! Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('course_name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('course__students')) {
            Schema::create('course__students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->unsignedBigInteger('course_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * @param  array<string, callable(Blueprint): void>  $columns
     */
    private function addColumns(string $tableName, array $columns): void
    {
        foreach ($columns as $column => $definition) {
            if (Schema::hasColumn($tableName, $column)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }
}
