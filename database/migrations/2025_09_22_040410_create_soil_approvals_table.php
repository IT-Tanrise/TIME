<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('soil_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soil_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable(); // For rejection reason
            $table->json('old_data'); // Current data before changes
            $table->json('new_data'); // Proposed changes
            $table->string('change_type'); // 'details' or 'costs'
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('soil_approvals');
    }
};