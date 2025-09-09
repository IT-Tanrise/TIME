<?php
// Migration: create_business_units_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('business_units')->onDelete('cascade');
            $table->index('parent_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_units');
    }
};