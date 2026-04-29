<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ivr;
use Illuminate\Http\Request;

class IvrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ivr::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('extension', 'like', "%{$search}%");
            });
        }

        // Filter by enabled
        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $ivrs = $query->orderBy('name')->paginate(25);

        return view('ivrs.index', compact('ivrs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ivrs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store method
        return redirect()->route('ivrs.index')->with('success', 'IVR created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ivr $ivr)
    {
        return view('ivrs.show', compact('ivr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ivr $ivr)
    {
        return view('ivrs.edit', compact('ivr'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ivr $ivr)
    {
        // TODO: Implement update method
        return redirect()->route('ivrs.index')->with('success', 'IVR updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ivr $ivr)
    {
        // TODO: Implement destroy method
        return redirect()->route('ivrs.index')->with('success', 'IVR deleted successfully');
    }
}
