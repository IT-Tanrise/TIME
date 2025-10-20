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
        Schema::create('land_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->onDelete('cascade');
            $table->string('certificate_type'); // SHM, SHGB, SHGU, SHP, etc.
            $table->string('certificate_number'); // varchar to allow 0xxx format
            $table->date('issued_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('issued_by')->nullable(); // BPN office name
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, expired, revoked
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for certificate-soil relationship
        Schema::create('certificate_soil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_certificate_id')->constrained('land_certificates')->onDelete('cascade');
            $table->foreignId('soil_id')->constrained('soils')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['land_certificate_id', 'soil_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_soil');
        Schema::dropIfExists('land_certificates');
    }
};