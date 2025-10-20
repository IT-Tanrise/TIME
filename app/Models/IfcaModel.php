<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfcaModel extends Model
{
    use HasFactory;
    protected $connection = 'IFCA_LIVE';
    // protected $table = 'mgr.pl_contract';

}
