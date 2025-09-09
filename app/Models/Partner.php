<?php
// app/Models/Partner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'name',
        'percentage'
    ];

    protected $casts = [
        'percentage' => 'decimal:2'
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function getFormattedPercentageAttribute()
    {
        return number_format($this->percentage, 2) . '%';
    }
}