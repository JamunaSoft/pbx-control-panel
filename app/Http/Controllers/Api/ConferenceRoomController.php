<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ConferenceRoom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConferenceRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ConferenceRoom::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $rooms = $query->orderBy('room_number')->paginate(25);

        return response()->json($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:conference_rooms|max:255',
            'name' => 'required|string|max:255',
            'pin' => 'nullable|string|max:10',
            'max_participants' => 'required|integer|min:1|max:100',
            'recording_enabled' => 'boolean',
            'wait_for_moderator' => 'boolean',
            'moderator_pin' => 'nullable|string|max:10',
            'mute_on_join' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $validated['enabled'] ?? true;

        $room = ConferenceRoom::create($validated);

        AuditLog::log('create', $room, $request->user(), [], $validated, "Created conference room {$room->room_number}");

        return response()->json([
            'room' => $room,
            'message' => 'Conference room created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ConferenceRoom $room)
    {
        return response()->json($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConferenceRoom $room)
    {
        $oldValues = $room->toArray();

        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique('conference_rooms')->ignore($room->id)],
            'name' => 'required|string|max:255',
            'pin' => 'nullable|string|max:10',
            'max_participants' => 'required|integer|min:1|max:100',
            'recording_enabled' => 'boolean',
            'wait_for_moderator' => 'boolean',
            'moderator_pin' => 'nullable|string|max:10',
            'mute_on_join' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $room->update($validated);

        AuditLog::log('update', $room, $request->user(), $oldValues, $validated, "Updated conference room {$room->room_number}");

        return response()->json([
            'room' => $room,
            'message' => 'Conference room updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ConferenceRoom $room)
    {
        $oldValues = $room->toArray();

        $room->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted conference room {$room->room_number}");

        return response()->json([
            'message' => 'Conference room deleted successfully',
        ]);
    }

    /**
     * Get active conferences (would integrate with Asterisk ARI)
     */
    public function active(Request $request)
    {
        // This would normally query Asterisk for active conferences
        // For now, return empty array
        return response()->json([
            'active_conferences' => [],
            'message' => 'Real-time conference data requires Asterisk ARI integration',
        ]);
    }

    /**
     * Kick participant from conference (would use ARI)
     */
    public function kickParticipant(Request $request, ConferenceRoom $room)
    {
        $validated = $request->validate([
            'participant_id' => 'required|string',
        ]);

        // This would normally use ARI to kick participant
        AuditLog::log('update', $room, $request->user(), [], [],
            "Attempted to kick participant {$validated['participant_id']} from conference room {$room->room_number}");

        return response()->json([
            'message' => 'Participant kick requires Asterisk ARI integration',
            'participant_id' => $validated['participant_id'],
        ]);
    }
}
