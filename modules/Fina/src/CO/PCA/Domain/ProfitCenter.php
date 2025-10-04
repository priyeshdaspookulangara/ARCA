<?php

namespace Modules\Fina\CO\PCA\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitCenter extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pca_profit_centers';

    protected $fillable = [
        'name',
        'description',
        'controlling_area_id',
        'responsible_person',
    ];

    public function postings()
    {
        return $this->hasMany(PcaPosting::class, 'profit_center_id');
    }
}