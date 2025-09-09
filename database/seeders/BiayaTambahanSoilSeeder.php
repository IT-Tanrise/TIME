<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryBiayaTambahanSoil;
use App\Models\DescriptionBiayaTambahanSoil;

class BiayaTambahanSoilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories and their descriptions
        $categoriesWithDescriptions = [
            'Legal Fees' => [
                'Notary fees for PPJB signing',
                'Legal document processing',
                'Contract drafting fees',
                'Legal consultation fees',
                'Document verification costs',
            ],
            'Tax & Government Fees' => [
                'BPHTB (Land and Building Transfer Tax)',
                'Stamp duty',
                'Registration fees',
                'Certificate processing fees',
                'Administrative fees',
                'Local government levies',
            ],
            'Survey & Measurement' => [
                'Land surveying costs',
                'Boundary marking fees',
                'Topographic survey',
                'Land measurement verification',
                'GPS coordinate mapping',
            ],
            'Insurance & Security' => [
                'Property insurance premium',
                'Title insurance',
                'Security deposit',
                'Risk assessment fees',
            ],
            'Transportation & Logistics' => [
                'Site visit transportation',
                'Document courier services',
                'Equipment transportation',
                'Personnel travel costs',
            ],
            'Due Diligence' => [
                'Property valuation fees',
                'Environmental assessment',
                'Soil quality testing',
                'Legal title verification',
                'Background check fees',
                'Financial audit costs',
            ],
            'Utilities & Infrastructure' => [
                'Electrical connection fees',
                'Water connection fees',
                'Road access improvement',
                'Drainage system installation',
                'Utility pole installation',
            ],
            'Brokerage & Commission' => [
                'Real estate agent commission',
                'Broker fees',
                'Marketing costs',
                'Advertisement expenses',
            ],
            'Miscellaneous' => [
                'Accommodation costs',
                'Meal allowances',
                'Communication expenses',
                'Printing and documentation',
                'Emergency funds',
                'Contingency costs',
            ],
        ];

        foreach ($categoriesWithDescriptions as $categoryName => $descriptions) {
            // Create category
            $category = CategoryBiayaTambahanSoil::create([
                'category' => $categoryName
            ]);

            // Create descriptions for this category
            foreach ($descriptions as $description) {
                DescriptionBiayaTambahanSoil::create([
                    'category_id' => $category->id,
                    'description' => $description
                ]);
            }
        }
    }
}