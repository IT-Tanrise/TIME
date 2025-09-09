<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;

class BusinessUnitSeeder extends Seeder
{
    public function run()
    {
        // Create PT A (parent)
        $ptA = BusinessUnit::create([
            'code' => 'PTA',
            'name' => 'PT A'
        ]);

        // Create PT B (child of PT A)
        $ptB = BusinessUnit::create([
            'code' => 'PTB',
            'name' => 'PT B',
            'parent_id' => $ptA->id
        ]);

        // Create PT D (child of PT A)
        BusinessUnit::create([
            'code' => 'PTD',
            'name' => 'PT D',
            'parent_id' => $ptA->id
        ]);

        // Create PT C (child of PT B)
        BusinessUnit::create([
            'code' => 'PTC',
            'name' => 'PT C',
            'parent_id' => $ptB->id
        ]);

        // Create PT E (child of PT B)
        BusinessUnit::create([
            'code' => 'PTE',
            'name' => 'PT E',
            'parent_id' => $ptB->id
        ]);

        // Create PT F (child of PT B)
        BusinessUnit::create([
            'code' => 'PTF',
            'name' => 'PT F',
            'parent_id' => $ptB->id
        ]);
    }
}
