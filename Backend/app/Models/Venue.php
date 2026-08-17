<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'slugs',
        'name',
        'area',
        'latitude',
        'longitude',
        'is_featured',
        'gcash_number',
        'maya_number',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function venueAdmins()
    {
        return $this->hasMany(VenueAdmin::class);
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'venue_admins');
    }

    public function courts()
    {
        return $this->hasMany(Court::class);
    }
}
