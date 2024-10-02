<?php

// app/Models/Room.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'time_slots', 'image_path', 'is_available'];
    
    // Define a relationship with Booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
