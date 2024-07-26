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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres',30);
            $table->string('apellidos',30);
            $table->string('dni',15)->unique();
            $table->string('provincia',20)->nullable();;
            $table->string('empresa',50)->nullable();
            $table->string('rubro',70)->nullable();
            $table->string('cargo',30)->nullable();
            $table->boolean('asistencia')->default(0);
            $table->string('rol')->default('user');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
