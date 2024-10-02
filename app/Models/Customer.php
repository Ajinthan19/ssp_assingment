<?php

// app/Models/Customer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['full_name', 'address', 'email', 'phone_number', 'occupation', 'username', 'password'];

    // Define a relationship with Booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
