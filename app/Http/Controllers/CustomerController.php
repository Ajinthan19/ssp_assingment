<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone_number' => 'required|string|max:15',
            'occupation' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:customers',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $customer = Customer::create([
            'full_name' => $request->full_name,
            'address' => $request->address,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'occupation' => $request->occupation,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Customer registered successfully',
            'customer' => $customer
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('username', $request->username)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'customer' => $customer,
            'token' => $token
        ], 200);
    }

    public function viewRooms()
    {
        // Fetch only rooms that are available
        $rooms = Room::where('is_available', true)->get();

        // Return the rooms as JSON response
        return response()->json($rooms);
    }

    // Method to book a room
    public function bookRoom(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'time_slot' => 'required|string',
            'payment_status' => 'required|boolean'
        ]);
    
        $room = Room::findOrFail($request->room_id);
    
        if (!$room->is_available) {
            return response()->json(['message' => 'Room is not available'], 400);
        }
    
        // Assume the customer pays successfully
        if ($request->payment_status) {
            $booking = new Booking();
            $booking->customer_id = auth()->id();
            $booking->room_id = $request->room_id;
            $booking->time_slot = $request->time_slot;
            $booking->status = 'confirmed'; // Confirmed after payment
            $booking->save();
    
            return response()->json([
                'message' => 'Booking confirmed!',
                'booking' => $booking
            ], 200);
        }
    
        return response()->json(['message' => 'Payment failed.'], 400);
    }
    public function getRooms()
{
    $rooms = Room::where('is_available', true)->get(['id', 'name', 'capacity', 'price_per_hour', 'time_slots']);
    return response()->json($rooms);
}
}
