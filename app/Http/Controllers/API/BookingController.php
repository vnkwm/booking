<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // print_r($request->all());
        // exit;
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'type' => 'required|in:full day,half day',
            'date' => 'required|date',
            'slot' => 'required|in:morning,evening',
            'time' => 'required|date_format:H:i'
        ]);

        $booking = Booking::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'date' => $request->date,
            'slot' => $request->slot,
            'time' => $request->time,
        ]);

        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
