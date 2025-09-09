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
        Schema::table('soils', function (Blueprint $table) {
            // Add new columns as nullable first
            $table->string('nomor_ppjb', 255)->nullable()->after('alamat_pembeli');
            $table->date('tanggal_ppjb')->nullable()->after('nomor_ppjb');
        });

        // Migrate data from old column to new columns
        $soils = \App\Models\Soil::all();
        foreach ($soils as $soil) {
            // Try to extract data from the old field
            $oldValue = $soil->nomor_dan_tanggal_ppjb;
            
            // Set default values
            $nomor_ppjb = $oldValue ?? '';
            $tanggal_ppjb = null;
            
            // Try to extract date if the format contains date-like patterns
            if ($oldValue) {
                // Look for date patterns (dd-mm-yyyy, dd/mm/yyyy, yyyy-mm-dd, etc.)
                if (preg_match('/(\d{1,2}[-\/]\d{1,2}[-\/]\d{4}|\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/', $oldValue, $matches)) {
                    try {
                        $dateString = $matches[1];
                        // Convert various date formats to Y-m-d
                        $date = \Carbon\Carbon::parse($dateString);
                        $tanggal_ppjb = $date->format('Y-m-d');
                        
                        // Remove the date part from the number
                        $nomor_ppjb = trim(str_replace($matches[0], '', $oldValue));
                        if (empty($nomor_ppjb)) {
                            $nomor_ppjb = $oldValue; // Keep original if we can't extract number part
                        }
                    } catch (\Exception $e) {
                        // If date parsing fails, keep the original value as nomor_ppjb
                        $nomor_ppjb = $oldValue;
                        $tanggal_ppjb = null;
                    }
                }
            }
            
            $soil->update([
                'nomor_ppjb' => $nomor_ppjb,
                'tanggal_ppjb' => $tanggal_ppjb,
            ]);
        }

        // Now make the columns NOT NULL and drop the old column
        Schema::table('soils', function (Blueprint $table) {
            // Make nomor_ppjb NOT NULL
            $table->string('nomor_ppjb', 255)->nullable(false)->change();
            
            // Drop old column
            $table->dropColumn('nomor_dan_tanggal_ppjb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            // Add back old column
            $table->string('nomor_dan_tanggal_ppjb')->after('alamat_pembeli');
        });

        // Migrate data back to old format
        $soils = \App\Models\Soil::all();
        foreach ($soils as $soil) {
            $combined = $soil->nomor_ppjb;
            if ($soil->tanggal_ppjb) {
                $combined .= ', ' . $soil->tanggal_ppjb->format('d M Y');
            }
            $soil->update(['nomor_dan_tanggal_ppjb' => $combined]);
        }

        Schema::table('soils', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['nomor_ppjb', 'tanggal_ppjb']);
        });
    }
};