<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(BusinessUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BusinessUnit::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Get hierarchical path (e.g., "PT A > PT B > PT C")
    public function getHierarchyPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    public function soils()
    {
        return $this->hasMany(Soil::class);
    }
}