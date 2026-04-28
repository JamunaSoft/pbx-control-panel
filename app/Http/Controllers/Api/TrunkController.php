<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trunk;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Trunk::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trunk_name', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
                  ->orWhere('host', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $trunks = $query->orderBy('trunk_name')->paginate(25);

        return response()->json($trunks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trunk_name' => 'required|string|unique:trunks',
            'provider' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'secret' => 'nullable|string|max:255',
            'context' => 'nullable|string|max:255',
            'type' => 'required|in:sip,iax,pjsip',
            'port' => 'nullable|integer|min:1|max:65535',
            'failover_enabled' => 'boolean',
            'failover_trunks' => 'nullable|array',
            'cost_per_minute' => 'nullable|numeric|min:0',
        ]);

        $validated['context'] = $validated['context'] ?? 'from-trunk';
        $validated['status'] = 'active';
        $validated['port'] = $validated['port'] ?? 5060;

        $trunk = Trunk::create($validated);

        AuditLog::log('create', $trunk, $request->user(), [], $validated, "Created trunk {$trunk->trunk_name}");

        return response()->json([
            'trunk' => $trunk,
            'message' => 'Trunk created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trunk $trunk)
    {
        return response()->json($trunk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trunk $trunk)
    {
        $oldValues = $trunk->toArray();

        $validated = $request->validate([
            'trunk_name' => ['required', 'string', Rule::unique('trunks')->ignore($trunk->id)],
            'provider' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'secret' => 'nullable|string|max:255',
            'context' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:sip,iax,pjsip',
            'port' => 'nullable|integer|min:1|max:65535',
            'failover_enabled' => 'boolean',
            'failover_trunks' => 'nullable|array',
            'cost_per_minute' => 'nullable|numeric|min:0',
        ]);

        $trunk->update($validated);

        AuditLog::log('update', $trunk, $request->user(), $oldValues, $validated, "Updated trunk {$trunk->trunk_name}");

        return response()->json([
            'trunk' => $trunk,
            'message' => 'Trunk updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Trunk $trunk)
    {
        $oldValues = $trunk->toArray();

        $trunk->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted trunk {$trunk->trunk_name}");

        return response()->json([
            'message' => 'Trunk deleted successfully'
        ]);
    }
}
