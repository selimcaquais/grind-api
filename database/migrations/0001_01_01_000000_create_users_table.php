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
        // Création de la table users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); // Remplacer 'name' par 'email' si c'est ce qui correspond
            $table->string('password');
            $table->timestamp('registration_date');
            $table->integer('user_streak')->default(0);
            $table->timestamps(0);
        });

        // Création de la table tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('iteration_max');
            $table->integer('streak')->default(0);
            $table->json('days');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clé étrangère vers users
            $table->timestamps(0);
        });

        // Création de la table realisations
        Schema::create('realisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clé étrangère vers users
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); // Clé étrangère vers tasks
            $table->timestamp('date');
            $table->integer('iteration');
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