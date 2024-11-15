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
        // Creation of users table 
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email',320)->unique();
            $table->string('password');
            $table->timestamp('registration_date');
            $table->integer('user_streak')->default(0);
            $table->timestamps(0);
            $table->string('timezone');
        });

        // Creation of tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('iteration_max');
            $table->integer('streak')->default(0);
            $table->json('days');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users
            $table->timestamps(0);
        });

        // Creation of realisation table
        Schema::create('realisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); // Foreign key to tasks
            $table->timestamp('date');
            $table->integer('iteration')->default(0);
            $table->integer('iteration_max');
            $table->integer('streak')->default(0);
            $table->timestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('realisations');
    }
};