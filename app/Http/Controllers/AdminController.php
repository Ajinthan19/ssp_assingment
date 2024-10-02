<?php

namespace App\Http\Controllers;
use App\Models\Customer; // Import the Customer model
use App\Models\Room; // Import the Customer model
use App\Models\TimeSlot; // Import the Customer model
use App\Models\Booking;

use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{
    // Show the admin login form
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Handle admin login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
    
        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            \Log::info('Admin logged in: ' . $request->username);
            return redirect()->route('admin.dashboard'); 
        }
        
        return back()->withErrors(['message' => 'Invalid login credentials']);
    }

    // Admin dashboard
   public function dashboard()
{
    return view('admin.dashboard'); // Ensure this view exists
}
    // Show user management
    // Manage users (show the list of registered customers)
    public function manageUsers()
    {
        // Fetch all customers from the database
        $customers = Customer::all();
        
        // Return the view with the customers data
        return view('admin.customers', compact('customers'));
    }

    // Delete a customer from the system
    public function deleteCustomer($id)
    {
        // Find the customer by ID and delete
        $customer = Customer::findOrFail($id);
        $customer->delete();

        // Redirect back to the user management page with success message
        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    
  // Show the form to create a new room
  public function createRoom()
  {
      return view('admin.create_room');
  }
  
  public function storeRoom(Request $request)
  {
      $request->validate([
          'name' => 'required|string|max:255',
          'capacity' => 'required|integer',
          'price' => 'required|numeric|min:0', // Validate price
          'time_slots' => 'nullable|array',
          'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      ]);
  
      $room = Room::create([
          'name' => $request->name,
          'capacity' => $request->capacity,
          'price' => $request->price, // Store price
          'time_slots' => json_encode($request->time_slots),
          'is_available' => true, // Set room as available
          'image_path' => $request->hasFile('image') ? $request->file('image')->store('room_images', 'public') : null,
      ]);
  
      return redirect()->route('admin.rooms')->with('success', 'Room created successfully.');
  }
// Update room
public function updateRoom(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'capacity' => 'required|integer',
        'time_slots' => 'nullable|array', // Allowing time slots as an array
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $room = Room::findOrFail($id);
    $room->name = $request->name;
    $room->capacity = $request->capacity;
    $room->time_slots = json_encode($request->time_slots); // Update time slots as JSON

    // Handle image upload, if applicable
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('room_images', 'public');
        $room->image_path = $path;
    }

    $room->save();

    return redirect()->route('admin.rooms')->with('success', 'Room updated successfully.');
}

public function deleteRoom($id)
{
    // Find the room and delete
    $room = Room::findOrFail($id);
    $room->delete();

    return redirect()->route('admin.rooms')->with('success', 'Room deleted successfully.');
}

public function manageRooms()
{
    // Fetch all rooms from the database
    $rooms = Room::all(); // This will return a Collection, even if there are no records.

    // Pass the rooms to the view
    return view('admin.manage_rooms', ['rooms' => $rooms]);
}

// Method to show all bookings with cancellation option
    // Method to show all bookings with cancellation option
    public function manageBookings()
    {
        // Fetch all bookings with related room and customer data
        $bookings = Booking::with('room', 'customer')->get();

        // Pass the bookings data to the view
        return view('admin.bookings', compact('bookings'));
    }

    // Method to cancel a booking
    public function cancelBooking($id)
    {
        // Find the booking by ID and delete it
        $booking = Booking::findOrFail($id);
        $booking->delete();

        // Redirect back to the bookings page with success message
        return redirect()->route('admin.bookings')->with('success', 'Booking canceled successfully.');
    }
    public function showBookings()
    {
        $bookings = Booking::with('room')->get(); // Fetch all bookings with their associated room
        return view('admin.bookings', compact('bookings'));
    }
    public function analytics()
    {
        $totalCustomers = Customer::count();
        $totalBookings = Booking::count();
        $totalEarnings = Booking::sum('amount'); // Assuming you have an 'amount' field in the Booking model
        $totalAvailableRooms = Room::where('is_available', true)->count();

        return view('admin.analytics', compact('totalCustomers', 'totalBookings', 'totalEarnings', 'totalAvailableRooms'));
    }
    public function editRoom($id)
    {
        // Fetch the room by ID
        $room = Room::findOrFail($id);
        
        // Return the view with the room data
        return view('admin.edit_room', compact('room'));
    }

}








