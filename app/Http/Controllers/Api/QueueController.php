<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallQueue;
use App\Models\Extension;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallQueue::with(['extensions'])->withCount('extensions');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('queue_name', 'like', "%{$search}%");
        }

        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $queues = $query->orderBy('queue_name')->paginate(25);

        return response()->json($queues);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'queue_name' => 'required|string|unique:queues|max:255',
            'strategy' => 'required|in:ringall,leastrecent,fewestcalls,random,rrmemory,linear',
            'timeout' => 'required|integer|min:1|max:300',
            'wrapuptime' => 'required|integer|min:0|max:300',
            'maxlen' => 'nullable|integer|min:0',
            'announce' => 'nullable|string|max:255',
            'context' => 'nullable|string|max:255',
            'servicelevel' => 'nullable|integer|min:0',
            'musicclass' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'extension_ids' => 'nullable|array',
            'extension_ids.*' => 'exists:extensions,id',
        ]);

        $validated['context'] = $validated['context'] ?? 'default';
        $validated['musicclass'] = $validated['musicclass'] ?? 'default';
        $validated['enabled'] = $validated['enabled'] ?? true;

        $queue = CallQueue::create($validated);

        // Attach extensions with penalties
        if ($request->has('extension_ids')) {
            $extensions = [];
            foreach ($request->extension_ids as $extensionId) {
                $extensions[$extensionId] = ['penalty' => 0]; // Default penalty
            }
            $queue->extensions()->attach($extensions);
        }

        AuditLog::log('create', $queue, $request->user(), [], $validated, "Created queue {$queue->queue_name}");

        return response()->json([
            'queue' => $queue->load(['extensions']),
            'message' => 'Queue created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CallQueue $queue)
    {
        return response()->json($queue->load(['extensions']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallQueue $queue)
    {
        $oldValues = $queue->toArray();

        $validated = $request->validate([
            'queue_name' => ['required', 'string', 'max:255', Rule::unique('queues')->ignore($queue->id)],
            'strategy' => 'required|in:ringall,leastrecent,fewestcalls,random,rrmemory,linear',
            'timeout' => 'required|integer|min:1|max:300',
            'wrapuptime' => 'required|integer|min:0|max:300',
            'maxlen' => 'nullable|integer|min:0',
            'announce' => 'nullable|string|max:255',
            'context' => 'nullable|string|max:255',
            'servicelevel' => 'nullable|integer|min:0',
            'musicclass' => 'nullable|string|max:255',
            'enabled' => 'boolean',
        ]);

        $queue->update($validated);

        AuditLog::log('update', $queue, $request->user(), $oldValues, $validated, "Updated queue {$queue->queue_name}");

        return response()->json([
            'queue' => $queue->load(['extensions']),
            'message' => 'Queue updated successfully'
        ]);
    }

    /**
     * Add extension to queue
     */
    public function addExtension(Request $request, CallQueue $queue)
    {
        $validated = $request->validate([
            'extension_id' => 'required|exists:extensions,id',
            'penalty' => 'integer|min:0|max:100',
        ]);

        $extension = Extension::find($validated['extension_id']);

        if ($queue->extensions()->where('extension_id', $extension->id)->exists()) {
            return response()->json(['error' => 'Extension already in queue'], 422);
        }

        $queue->extensions()->attach($extension->id, ['penalty' => $validated['penalty'] ?? 0]);

        AuditLog::log('update', $queue, $request->user(), [], [],
            "Added extension {$extension->extension_number} to queue {$queue->queue_name}");

        return response()->json([
            'message' => 'Extension added to queue successfully',
            'queue' => $queue->load(['extensions'])
        ]);
    }

    /**
     * Remove extension from queue
     */
    public function removeExtension(Request $request, CallQueue $queue, Extension $extension)
    {
        $queue->extensions()->detach($extension->id);

        AuditLog::log('update', $queue, $request->user(), [], [],
            "Removed extension {$extension->extension_number} from queue {$queue->queue_name}");

        return response()->json([
            'message' => 'Extension removed from queue successfully',
            'queue' => $queue->load(['extensions'])
        ]);
    }

    /**
     * Update extension penalty in queue
     */
    public function updateExtensionPenalty(Request $request, CallQueue $queue, Extension $extension)
    {
        $validated = $request->validate([
            'penalty' => 'required|integer|min:0|max:100',
        ]);

        $queue->extensions()->updateExistingPivot($extension->id, ['penalty' => $validated['penalty']]);

        AuditLog::log('update', $queue, $request->user(), [], [],
            "Updated penalty for extension {$extension->extension_number} in queue {$queue->queue_name} to {$validated['penalty']}");

        return response()->json([
            'message' => 'Extension penalty updated successfully',
            'queue' => $queue->load(['extensions'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CallQueue $queue)
    {
        $oldValues = $queue->toArray();

        $queue->extensions()->detach(); // Remove all extensions first
        $queue->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted queue {$queue->queue_name}");

        return response()->json([
            'message' => 'Queue deleted successfully'
        ]);
    }
}
