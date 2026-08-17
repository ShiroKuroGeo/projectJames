<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueClosedDates extends Model
{
    public $table = 'venue_closed_dates';

    protected $fillable = [
        'venue_id',
        'closed_date',
        'reason',
    ];

    public function venue() {
        return $this->belongsTo(Venue::class);
    }
}
