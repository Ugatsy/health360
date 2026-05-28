<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BodyRegion;

class BodyRegionsSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'Head', 'name_medical' => 'Cranium', 'is_critical_region' => true, 'sort_order' => 1],
            ['name' => 'Neck', 'name_medical' => 'Cervical', 'is_critical_region' => true, 'sort_order' => 2],
            ['name' => 'Chest', 'name_medical' => 'Thorax', 'is_critical_region' => true, 'sort_order' => 3],
            ['name' => 'Abdomen', 'name_medical' => 'Abdomen', 'is_critical_region' => false, 'sort_order' => 4],
            ['name' => 'Lower Back', 'name_medical' => 'Lumbar', 'is_critical_region' => false, 'sort_order' => 5],
            ['name' => 'Left Shoulder', 'name_medical' => 'Left Glenohumeral', 'is_critical_region' => false, 'sort_order' => 6],
            ['name' => 'Right Shoulder', 'name_medical' => 'Right Glenohumeral', 'is_critical_region' => false, 'sort_order' => 7],
            ['name' => 'Left Arm', 'name_medical' => 'Left Upper Extremity', 'is_critical_region' => false, 'sort_order' => 8],
            ['name' => 'Right Arm', 'name_medical' => 'Right Upper Extremity', 'is_critical_region' => false, 'sort_order' => 9],
            ['name' => 'Left Hand', 'name_medical' => 'Left Hand', 'is_critical_region' => false, 'sort_order' => 10],
            ['name' => 'Right Hand', 'name_medical' => 'Right Hand', 'is_critical_region' => false, 'sort_order' => 11],
            ['name' => 'Left Hip', 'name_medical' => 'Left Hip', 'is_critical_region' => false, 'sort_order' => 12],
            ['name' => 'Right Hip', 'name_medical' => 'Right Hip', 'is_critical_region' => false, 'sort_order' => 13],
            ['name' => 'Left Leg', 'name_medical' => 'Left Lower Extremity', 'is_critical_region' => false, 'sort_order' => 14],
            ['name' => 'Right Leg', 'name_medical' => 'Right Lower Extremity', 'is_critical_region' => false, 'sort_order' => 15],
            ['name' => 'Left Foot', 'name_medical' => 'Left Foot', 'is_critical_region' => false, 'sort_order' => 16],
            ['name' => 'Right Foot', 'name_medical' => 'Right Foot', 'is_critical_region' => false, 'sort_order' => 17],
        ];

        foreach ($regions as $region) {
            BodyRegion::create($region);
        }
    }
}
