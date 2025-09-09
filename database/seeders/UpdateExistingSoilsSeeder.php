<?php
// database/seeders/UpdateExistingSoilsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Soil;
use App\Models\User;
use App\Models\SoilHistory;

class UpdateExistingSoilsSeeder extends Seeder
{
    public function run()
    {
        // Get the first admin user or create a system user
        $systemUser = User::where('email', 'admin@admin.com')->first() 
                     ?? User::first() 
                     ?? User::create([
                         'name' => 'System',
                         'email' => 'system@system.com',
                         'password' => bcrypt('password'),
                         'email_verified_at' => now(),
                     ]);

        // Update all existing soil records without created_by/updated_by
        Soil::whereNull('created_by')
            ->orWhereNull('updated_by')
            ->chunk(100, function ($soils) use ($systemUser) {
                foreach ($soils as $soil) {
                    $soil->update([
                        'created_by' => $soil->created_by ?? $systemUser->id,
                        'updated_by' => $soil->updated_by ?? $systemUser->id,
                    ]);

                    // Create initial history record for existing records
                    SoilHistory::create([
                        'soil_id' => $soil->id,
                        'user_id' => $systemUser->id,
                        'action' => 'created',
                        'changes' => [],
                        'old_values' => null,
                        'new_values' => $soil->getAttributes(),
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'System Migration',
                        'created_at' => $soil->created_at,
                        'updated_at' => $soil->created_at,
                    ]);
                }
            });

        $this->command->info('Updated existing soil records with user tracking and history.');
    }
}