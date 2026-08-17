<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueAdmin extends Model
{
    public $table = 'venue_admins';

    protected $fillable = [
        'user_id',
        'venue_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venue () {
        return $this->belongsTo(Venue::class);
    }

}
