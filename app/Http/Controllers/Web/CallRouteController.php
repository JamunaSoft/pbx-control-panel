<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CallRoute;
use Illuminate\Http\Request;

class CallRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallRoute::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('pattern', 'like', "%{$search}%");
            });
        }

        // Filter by enabled
        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $callRoutes = $query->orderBy('priority')->paginate(25);

        return view('call-routes.index', compact('callRoutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('call-routes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store method
        return redirect()->route('call-routes.index')->with('success', 'Call route created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(CallRoute $callRoute)
    {
        return view('call-routes.show', compact('callRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallRoute $callRoute)
    {
        return view('call-routes.edit', compact('callRoute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallRoute $callRoute)
    {
        // TODO: Implement update method
        return redirect()->route('call-routes.index')->with('success', 'Call route updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CallRoute $callRoute)
    {
        // TODO: Implement destroy method
        return redirect()->route('call-routes.index')->with('success', 'Call route deleted successfully');
    }
}
