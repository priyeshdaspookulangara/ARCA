<?php

namespace Modules\GRC\ProcessControl\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoDRule extends Model
{
    use HasFactory;

    protected $table = 'grc_sod_rules';

    protected $fillable = [
        'name',
        'condition_json',
        'enforcement_mode',
    ];

    protected $casts = [
        'condition_json' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\GRC\Database\factories\SoDRuleFactory::new();
    }
}