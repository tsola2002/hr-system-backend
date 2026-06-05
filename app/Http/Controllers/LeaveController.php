<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Leave::query();

        if ($user->isManager()) {
            // Managers see pending leaves for their subordinates + all their own leaves
            $subordinateIds = $user->subordinates()->pluck('id')->toArray();
            $query->where(function ($q) use ($user, $subordinateIds) {
                $q->whereIn('user_id', $subordinateIds)
                  ->orWhere('user_id', $user->id);
            });
        } else {
            // Employees see only their own leaves
            $query->where('user_id', $user->id);
        }

        if ($request->status && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_half_day' => 'boolean',
            'reason' => 'nullable|string',
        ]);

        $days = ($data['is_half_day'] ?? false) ? "0.5 Day(s)" : "1 Day(s)";

        $leave = Leave::create([
            'user_id' => Auth::id(),
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_half_day' => $data['is_half_day'] ?? false,
            'total_days' => $days,
            'status' => 'Pending',
        ]);

        return response()->json($leave, 201);
    }

    public function show(string $id)
    {
        $leave = Leave::findOrFail($id);

        $this->authorize($leave);

        return response()->json($leave);
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $this->authorize($leave);

        // Employees can only edit their own pending leaves before approval
        if (!Auth::user()->isManager() && !$leave->isPending()) {
            return response()->json(['message' => 'Cannot edit approved or rejected leaves'], 403);
        }

        // Managers cannot update status via this endpoint - use approve/reject instead
        if ($request->has('status') && Auth::user()->isManager()) {
            return response()->json(['message' => 'Use /approve or /reject endpoints to change status'], 403);
        }

        $leave->update($request->all());

        return response()->json($leave);
    }

    public function destroy(string $id)
    {
        $leave = Leave::findOrFail($id);

        $this->authorize($leave);

        Leave::destroy($id);

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $user = Auth::user();

        if (!$user->isManager()) {
            return response()->json(['message' => 'Only managers can approve leaves'], 403);
        }

        if ($leave->employee->manager_id !== $user->id) {
            return response()->json(['message' => 'You can only approve leaves for your subordinates'], 403);
        }

        if (!$leave->isPending()) {
            return response()->json(['message' => 'Cannot approve a leave that is not pending'], 400);
        }

        $leave->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json($leave);
    }

    public function reject(Request $request, $id)
    {
        $data = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $leave = Leave::findOrFail($id);
        $user = Auth::user();

        if (!$user->isManager()) {
            return response()->json(['message' => 'Only managers can reject leaves'], 403);
        }

        if ($leave->employee->manager_id !== $user->id) {
            return response()->json(['message' => 'You can only reject leaves for your subordinates'], 403);
        }

        if (!$leave->isPending()) {
            return response()->json(['message' => 'Cannot reject a leave that is not pending'], 400);
        }

        $leave->update([
            'status' => 'Rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return response()->json($leave);
    }

    private function authorize(Leave $leave): void
    {
        $user = Auth::user();

        $canAccess = $user->id === $leave->user_id ||
                     ($user->isManager() && $user->id === $leave->employee->manager_id);

        if (!$canAccess) {
            abort(403, 'Unauthorized to access this leave');
        }
    }
}

