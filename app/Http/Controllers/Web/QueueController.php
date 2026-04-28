<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CallQueue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallQueue::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('queue_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by enabled
        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $queues = $query->orderBy('queue_name')->paginate(25);

        return view('queues.index', compact('queues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('queues.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store method
        return redirect()->route('queues.index')->with('success', 'Queue created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(CallQueue $queue)
    {
        return view('queues.show', compact('queue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallQueue $queue)
    {
        return view('queues.edit', compact('queue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallQueue $queue)
    {
        // TODO: Implement update method
        return redirect()->route('queues.index')->with('success', 'Queue updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CallQueue $queue)
    {
        // TODO: Implement destroy method
        return redirect()->route('queues.index')->with('success', 'Queue deleted successfully');
    }

    /**
     * Add extension to queue.
     */
    public function addExtension(Request $request, CallQueue $queue)
    {
        // TODO: Implement add extension
        return redirect()->back()->with('success', 'Extension added to queue');
    }

    /**
     * Remove extension from queue.
     */
    public function removeExtension(Request $request, CallQueue $queue, $extension)
    {
        // TODO: Implement remove extension
        return redirect()->back()->with('success', 'Extension removed from queue');
    }
}
