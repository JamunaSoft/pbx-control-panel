<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Trunk;
use Illuminate\Http\Request;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Trunk::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('host', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $trunks = $query->orderBy('trunk_name')->paginate(25);

        return view('trunks.index', compact('trunks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trunks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trunk_name' => 'required|string|max:255|unique:trunks,trunk_name',
            'provider' => 'nullable|string|max:255',
            'host' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:sip,iax,pjsip',
        ]);

        try {
            Trunk::create($validated);

            return redirect()->route('trunks.index')->with('success', 'Trunk created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create trunk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trunk $trunk)
    {
        return view('trunks.show', compact('trunk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trunk $trunk)
    {
        return view('trunks.edit', compact('trunk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trunk $trunk)
    {
        $validated = $request->validate([
            'trunk_name' => 'required|string|max:255|unique:trunks,trunk_name,' . $trunk->id,
            'provider' => 'nullable|string|max:255',
            'host' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:sip,iax,pjsip',
        ]);

        try {
            $trunk->update($validated);

            return redirect()->route('trunks.index')->with('success', 'Trunk updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update trunk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Trunk $trunk)
    {
        try {
            $trunk->delete();

            return redirect()->route('trunks.index')->with('success', 'Trunk deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete trunk');
        }
    }
}
