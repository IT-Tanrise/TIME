<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlProject extends Model
{
    protected $table = 'mgr_pl_project';
    protected $primaryKey = 'project_no';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'entity_cd',
        'project_no',
        'descs',
        'project_type',
        'location',
        'contract_amt',
        'auth_vo',
        'claim_todate',
        'start_date',
        'completion_date',
        'project_status',
    ];

    protected $casts = [
        'contract_amt' => 'decimal:2',
        'auth_vo' => 'decimal:2',
        'claim_todate' => 'decimal:2',
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    // Relationships
    public function entity(): BelongsTo
    {
        return $this->belongsTo(CfEntity::class, 'entity_cd', 'entity_cd');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PlContract::class, 'project_no', 'project_no');
    }
}