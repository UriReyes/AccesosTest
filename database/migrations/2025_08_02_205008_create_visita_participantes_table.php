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
        Schema::create('visita_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_id')->constrained('visitas')->onDelete('cascade');
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('status')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita_participantes');
    }
};
