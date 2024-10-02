<?php
// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Show available slots for a room
    public function showAvailableSlots($roomId)
    {
        $room = Room::findOrFail($roomId);
        return response()->json(['time_slots' => json_decode($room->time_slots)]);
    }

    // Book time slots
    public function bookSlots(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'total_price' => 'required|numeric',
        ]);

        $booking = Booking::create([
            'customer_id' => Auth::id(), // Assuming the customer is logged in
            'room_id' => $request->room_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => $request->total_price,
        ]);

        return response()->json(['message' => 'Booking successful!', 'booking' => $booking], 201);
    }
}
