<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConferenceRoom;
use Illuminate\Http\Request;

class ConferenceRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ConferenceRoom::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        // Filter by enabled
        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $conferenceRooms = $query->orderBy('room_number')->paginate(25);

        return view('conference-rooms.index', compact('conferenceRooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('conference-rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store method
        return redirect()->route('conference-rooms.index')->with('success', 'Conference room created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ConferenceRoom $conferenceRoom)
    {
        return view('conference-rooms.show', compact('conferenceRoom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConferenceRoom $conferenceRoom)
    {
        return view('conference-rooms.edit', compact('conferenceRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConferenceRoom $conferenceRoom)
    {
        // TODO: Implement update method
        return redirect()->route('conference-rooms.index')->with('success', 'Conference room updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ConferenceRoom $conferenceRoom)
    {
        // TODO: Implement destroy method
        return redirect()->route('conference-rooms.index')->with('success', 'Conference room deleted successfully');
    }
}
