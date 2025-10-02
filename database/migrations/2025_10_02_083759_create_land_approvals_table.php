<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('change_type', ['details', 'delete', 'create']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_approvals');
    }
};