<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtCloseTime extends Model
{
    protected $table = 'court_closed_times';

    protected $fillable = [
        'court_id',
        'closed_date',
        'closed_times',
    ];

    protected function casts(): array
    {
        return [
            'closed_date' => 'date',
            'closed_times' => 'array',
        ];
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
