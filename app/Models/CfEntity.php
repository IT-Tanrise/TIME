<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CfEntity extends Model
{
    protected $table = 'mgr_cf_entity';
    protected $primaryKey = 'entity_cd';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'entity_cd',
        'entity_name',
        'address1',
        'address2',
        'address3',
        'telephone_no',
        'fax_no',
    ];

    // Relationships
    public function projects(): HasMany
    {
        return $this->hasMany(PlProject::class, 'entity_cd', 'entity_cd');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PlContract::class, 'entity_cd', 'entity_cd');
    }
}