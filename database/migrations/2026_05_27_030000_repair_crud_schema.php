<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('degrees')) {
            Schema::create('degrees', function (Blueprint $table) {
                $table->id();
                $table->string('degree_title')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('fname');
                $table->string('mname')->nullable();
                $table->string('lname');
                $table->string('email');
                $table->string('contactno');
                $table->string('degree_title')->nullable();
                $table->unsignedBigInteger('degree_id')->nullable();
                $table->unsignedBigInteger('user_account_id')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'fname')) {
                    $table->string('fname')->nullable();
                }

                if (! Schema::hasColumn('students', 'mname')) {
                    $table->string('mname')->nullable();
                }

                if (! Schema::hasColumn('students', 'lname')) {
                    $table->string('lname')->nullable();
                }

                if (! Schema::hasColumn('students', 'email')) {
                    $table->string('email')->nullable();
                }

                if (! Schema::hasColumn('students', 'contactno')) {
                    $table->string('contactno')->nullable();
                }

                if (! Schema::hasColumn('students', 'degree_title')) {
                    $table->string('degree_title')->nullable();
                }

                if (! Schema::hasColumn('students', 'degree_id')) {
                    $table->unsignedBigInteger('degree_id')->nullable();
                }

                if (! Schema::hasColumn('students', 'user_account_id')) {
                    $table->unsignedBigInteger('user_account_id')->nullable();
                }

                if (! Schema::hasColumn('students', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
            });
        }

        if (! Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('fname');
                $table->string('mname')->nullable();
                $table->string('lname');
                $table->string('email');
                $table->string('contactno');
                $table->unsignedBigInteger('user_account_id')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('teachers', function (Blueprint $table) {
                if (! Schema::hasColumn('teachers', 'fname')) {
                    $table->string('fname')->nullable();
                }

                if (! Schema::hasColumn('teachers', 'mname')) {
                    $table->string('mname')->nullable();
                }

                if (! Schema::hasColumn('teachers', 'lname')) {
                    $table->string('lname')->nullable();
                }

                if (! Schema::hasColumn('teachers', 'email')) {
                    $table->string('email')->nullable();
                }

                if (! Schema::hasColumn('teachers', 'contactno')) {
                    $table->string('contactno')->nullable();
                }

                if (! Schema::hasColumn('teachers', 'user_account_id')) {
                    $table->unsignedBigInteger('user_account_id')->nullable();
                }
            });
        }
    }
};
