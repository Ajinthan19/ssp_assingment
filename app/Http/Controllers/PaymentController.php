<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'source' => 'required|string', // The Stripe token from client-side
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a charge
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => 'usd',
                'source' => $request->source,
                'description' => 'Room Booking Payment',
            ]);

            return response()->json(['message' => 'Payment successful', 'charge' => $charge], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }
}
