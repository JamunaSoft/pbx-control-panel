<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ivr;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IvrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ivr::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $ivrs = $query->orderBy('name')->paginate(25);

        return response()->json($ivrs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ivrs|max:255',
            'greeting_audio' => 'nullable|string|max:255',
            'timeout_action' => 'required|in:hangup,repeat,extension,queue',
            'timeout_seconds' => 'required|integer|min:1|max:300',
            'menu_options' => 'required|array|min:1',
            'menu_options.*.key' => 'required|string|size:1',
            'menu_options.*.action' => 'required|in:extension,queue,ivr,hangup',
            'menu_options.*.destination' => 'required_if:menu_options.*.action,extension,queue,ivr|string',
            'invalid_input_action' => 'required|in:repeat,hangup',
            'max_attempts' => 'required|integer|min:1|max:10',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $validated['enabled'] ?? true;

        $ivr = Ivr::create($validated);

        AuditLog::log('create', $ivr, $request->user(), [], $validated, "Created IVR {$ivr->name}");

        return response()->json([
            'ivr' => $ivr,
            'message' => 'IVR created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ivr $ivr)
    {
        return response()->json($ivr);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ivr $ivr)
    {
        $oldValues = $ivr->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ivrs')->ignore($ivr->id)],
            'greeting_audio' => 'nullable|string|max:255',
            'timeout_action' => 'required|in:hangup,repeat,extension,queue',
            'timeout_seconds' => 'required|integer|min:1|max:300',
            'menu_options' => 'required|array|min:1',
            'menu_options.*.key' => 'required|string|size:1',
            'menu_options.*.action' => 'required|in:extension,queue,ivr,hangup',
            'menu_options.*.destination' => 'required_if:menu_options.*.action,extension,queue,ivr|string',
            'invalid_input_action' => 'required|in:repeat,hangup',
            'max_attempts' => 'required|integer|min:1|max:10',
            'enabled' => 'boolean',
        ]);

        $ivr->update($validated);

        AuditLog::log('update', $ivr, $request->user(), $oldValues, $validated, "Updated IVR {$ivr->name}");

        return response()->json([
            'ivr' => $ivr,
            'message' => 'IVR updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ivr $ivr)
    {
        $oldValues = $ivr->toArray();

        $ivr->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted IVR {$ivr->name}");

        return response()->json([
            'message' => 'IVR deleted successfully'
        ]);
    }
}
