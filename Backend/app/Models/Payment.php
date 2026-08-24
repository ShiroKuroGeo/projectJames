<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    public $table = 'payments';
    
    protected $fillable = [
        'image',
        'user_id',
        'payment_type'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

}
