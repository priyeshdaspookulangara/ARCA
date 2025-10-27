<?php

namespace Modules\TaxEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxAuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['action', 'performed_by', 'before', 'after', 'timestamp'];
}
