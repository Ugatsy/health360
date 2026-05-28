<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyRegion extends Model
{
    protected $fillable = [
        'parent_region_id',
        'name',
        'name_medical',
        'description',
        'threejs_mesh_id',
        'bounding_coordinates',
        'icd10_code_prefix',
        'is_critical_region',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'bounding_coordinates' => 'array',
        'is_critical_region' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ========== Relationships ==========

    public function parent()
    {
        return $this->belongsTo(BodyRegion::class, 'parent_region_id');
    }

    public function children()
    {
        return $this->hasMany(BodyRegion::class, 'parent_region_id');
    }

    public function symptomEntries()
    {
        return $this->hasMany(SymptomEntry::class);
    }

    // ========== Scopes ==========

    public function scopeCritical($query)
    {
        return $query->where('is_critical_region', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootRegions($query)
    {
        return $query->whereNull('parent_region_id');
    }

    // ========== Accessors ==========

    public function getDisplayNameAttribute()
    {
        return $this->name_medical
            ? "{$this->name} ({$this->name_medical})"
            : $this->name;
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' → ', $path);
    }
}
