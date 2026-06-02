<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Leave::query();

        // filters from frontend
        if ($request->status && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        return response()->json($query->latest()->get());
    }

    // POST /api/leaves
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_half_day' => 'boolean',
            'reason' => 'nullable|string',
        ]);

        $days = $data['is_half_day'] ?? false ? "0.5 Day(s)" : "1 Day(s)";

        $leave = Leave::create([
            'user' => 'Admin',
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_half_day' => $data['is_half_day'] ?? false,
            'total_days' => $days,
            'status' => 'Pending',
        ]);

        return response()->json($leave, 201);
    }


    // GET /api/leaves/{id}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Leave::findOrFail($id);
    }


    // PUT /api/leaves/{id}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $leave->update($request->all());

        return response()->json($leave);
    }


    // DELETE /api/leaves/{id}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Leave::destroy($id);

        return response()->json(['message' => 'Deleted successfully']);
    }
}
