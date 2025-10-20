<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ShiftID',
        'TerminalID',
        'UserID',
        'Activity',
    ];

    protected $table = 'offline_activity_log';
}
