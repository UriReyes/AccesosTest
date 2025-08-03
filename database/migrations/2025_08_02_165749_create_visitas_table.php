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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();

            $table->string('visitor_name')->nullable();    // For external visitors
            $table->string('visitor_email')->nullable();   // For external visitors
            $table->string('visitor_company')->nullable(); // For external visitors
            $table->string('visitor_reason')->nullable();  // For external visitors

            $table->string('time_start'); // Start time as string (H:i format)
            $table->string('time_end');   // End time as string (H:i format)
            $table->json('dates');        // Selected dates (one or more)
            $table->enum('type', ['interna', 'externa'])->default('interna');
            $table->foreignId('sede_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');                           // Requester user
            $table->foreignId('authorized_by')->nullable()->constrained('users')->onDelete('set null'); // Authorizer user

            $table->string('status')->default('pending');
            $table->uuid('qr_token')->unique()->nullable(); // QR validation token
            $table->timestamp('qr_used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
