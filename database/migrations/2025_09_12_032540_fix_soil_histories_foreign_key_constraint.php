<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixSoilHistoriesForeignKeyConstraint extends Migration
{
    public function up()
    {
        Schema::table('soil_histories', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['soil_id']);
            
            // Re-add with proper cascade deletion
            $table->foreign('soil_id')
                  ->references('id')
                  ->on('soils')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('soil_histories', function (Blueprint $table) {
            // Revert back to original constraint
            $table->dropForeign(['soil_id']);
            $table->foreign('soil_id')
                  ->references('id')
                  ->on('soils');
        });
    }
}