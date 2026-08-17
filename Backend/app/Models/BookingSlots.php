<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlots extends Model
{
    public $table = 'booking_slots';

    protected $fillable = [
        'booking_id',
        'slot_date',
        'start_time1',
        'end_time',
    ];

    public function booking (){
        return $this->belongsTo(Booking::class);
    }
}
