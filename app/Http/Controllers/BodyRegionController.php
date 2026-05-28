<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyRegion;

class BodyRegionController extends Controller
{
    public function getRegions()
    {
        $regions = BodyRegion::active()
            ->with('children')
            ->orderBy('sort_order')
            ->get()
            ->map(function($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'name_medical' => $region->name_medical,
                    'is_critical' => $region->is_critical_region,
                    'mesh_id' => $region->threejs_mesh_id,
                    'coordinates' => $region->bounding_coordinates,
                    'children' => $region->children->map(function($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                        ];
                    })
                ];
            });

        return response()->json($regions);
    }
}
