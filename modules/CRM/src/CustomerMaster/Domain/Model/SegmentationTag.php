<?php

namespace Modules\CRM\CustomerMaster\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SegmentationTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Create a new factory instance for the model.
     *
      * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\SegmentationTagFactory::new();
    }
}