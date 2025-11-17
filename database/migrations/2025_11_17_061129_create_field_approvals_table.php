<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')
                ->constrained('fields')
                ->onDelete('cascade');
            $table->foreignId('requested_by')
                ->constrained('users')
                ->onDelete('restrict');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('reason_delete')->nullable();
            $table->json('old_values')->nullable()->comment('Data sebelum perubahan');
            $table->json('new_values')->nullable()->comment('Data setelah perubahan');
            $table->enum('change_type', ['create', 'update', 'delete']);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes untuk performa query
            $table->index(['field_id', 'status']);
            $table->index('requested_by');
            $table->index('approved_by');
            $table->index('approved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_approvals');
    }
};
