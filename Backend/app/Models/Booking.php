<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'venue_id',
        'court_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'booking_date',
        'start_time',
        'end_time',
        'hours',
        'amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    public function user (){
        return $this->belongsTo(User::class);
    }

    public function venue (){
        return $this->belongsTo(Venue::class);
    }

    public function court(){
        return $this->belongsTo(Court::class);
    }
}
