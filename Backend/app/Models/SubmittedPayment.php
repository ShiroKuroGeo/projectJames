<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmittedPayment extends Model
{
    protected $table = 'submitted_payments';

    protected $fillable = [
        'payment_id',
        'booking_id',
        'image'
    ];

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function booking(){
        return $this->belongsTo(Booking::class);
    }

}