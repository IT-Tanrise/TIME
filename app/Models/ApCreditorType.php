<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApCreditorType extends Model
{
    protected $table = 'mgr_ap_creditor_type';
    protected $primaryKey = 'creditor_type';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'creditor_type',
        'descs',
        'control_acct',
        'accrual_acct',
    ];

    // Relationships
    public function creditors(): HasMany
    {
        return $this->hasMany(ApCreditor::class, 'type', 'creditor_type');
    }
}