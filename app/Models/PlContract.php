<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlContract extends Model
{
    protected $table = 'mgr_pl_contract';
    protected $primaryKey = 'contract_no';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'contract_no',
        'project_no',
        'contract_descs',
        'entity_cd',
        'contractor_id',
        'creditor_acct',
        'contract_amt',
        'auth_vo',
        'ret_limit',
        'ret_percent',
        'claim_todate',
        'ret_todate',
        'start_dt',
        'end_dt',
        'award_dt',
        'complete_dt',
        'subcon_status',
        'remarks',
    ];

    protected $casts = [
        'contract_amt' => 'decimal:2',
        'auth_vo' => 'decimal:2',
        'ret_limit' => 'decimal:2',
        'claim_todate' => 'decimal:2',
        'ret_todate' => 'decimal:2',
        'start_dt' => 'datetime',
        'end_dt' => 'datetime',
        'award_dt' => 'datetime',
        'complete_dt' => 'datetime',
    ];

    // Relationships
    public function creditor(): BelongsTo
    {
        return $this->belongsTo(ApCreditor::class, 'creditor_acct', 'creditor_acct');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PlProject::class, 'project_no', 'project_no');
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(CfEntity::class, 'entity_cd', 'entity_cd');
    }

    // Accessors
    public function getFormattedContractAmtAttribute(): string
    {
        return 'Rp ' . number_format($this->contract_amt, 0, ',', '.');
    }

    public function getFormattedAwardDtAttribute(): ?string
    {
        return $this->award_dt ? $this->award_dt->format('d M Y') : null;
    }

    public function getFormattedStartDtAttribute(): ?string
    {
        return $this->start_dt ? $this->start_dt->format('d M Y') : null;
    }

    public function getFormattedEndDtAttribute(): ?string
    {
        return $this->end_dt ? $this->end_dt->format('d M Y') : null;
    }

    public function getContractStatusAttribute(): string
    {
        if ($this->subcon_status === 'C') {
            return 'Completed';
        } elseif ($this->subcon_status === 'A') {
            return 'Active';
        } elseif ($this->subcon_status === 'T') {
            return 'Terminated';
        }
        return 'Draft';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->subcon_status) {
            'C' => 'bg-green-100 text-green-800',
            'A' => 'bg-blue-100 text-blue-800',
            'T' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}