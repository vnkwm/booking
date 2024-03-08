<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::latest()->paginate($request->input('per_page', 10));

        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|string',
            'email' => 'bail|required|email',
            'type' => 'bail|required|in:full day,half day',
            'date' => 'bail|required|date',
            'slot' => 'bail|required|in:morning,evening',
            'time' => 'bail|required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()->first()], 422);
        }

        $existingBooking = Booking::where('date', $request->date)
            ->where('slot', $request->slot)
            ->first();

        if ($existingBooking) {
            return response()->json(['error' => 'Booking already exists for this date and slot'], 400);
        }

        if ($request->type === 'full day' && $existingBooking) {
            return response()->json(['error' => 'Full day booking already exists for this date'], 400);
        }

        if ($request->type === 'half day' && $existingBooking) {
            return response()->json(['error' => 'Half day booking already exists for this date'], 400);
        }

        if ($request->type === 'half day' && $request->slot) {
            $conflictingFullDayBooking = Booking::where('date', $request->date)
                ->where('type', 'full day')
                ->first();

            if ($conflictingFullDayBooking) {
                return response()->json(['error' => 'Full day booking conflicts with existing half day booking'], 400);
            }
        }

        $booking = Booking::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'date' => $request->date,
            'slot' => $request->slot,
            'time' => $request->time,
        ]);

        return response()->json($booking, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        return response()->json($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|string',
            'email' => 'bail|required|email',
            'type' => 'bail|required|in:full day,half day',
            'date' => 'bail|required|date',
            'slot' => 'bail|required|in:morning,evening',
            'time' => 'bail|required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()->first()], 422);
        }

        $booking->update($request->all());

        return response()->json($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
