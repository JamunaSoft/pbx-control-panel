<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('pattern', 'like', "%{$search}%");
            });
        }

        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        if ($request->has('destination_type')) {
            $query->where('destination_type', $request->destination_type);
        }

        $routes = $query->orderBy('priority')->orderBy('pattern')->paginate(25);

        return response()->json($routes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'destination_type' => 'required|in:extension,queue,ivr,trunk',
            'destination_value' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:100',
            'context' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'time_conditions' => 'nullable|array',
        ]);

        $validated['context'] = $validated['context'] ?? 'default';
        $validated['enabled'] = $validated['enabled'] ?? true;

        $route = CallRoute::create($validated);

        AuditLog::log('create', $route, $request->user(), [], $validated, "Created call route {$route->name}");

        return response()->json([
            'route' => $route,
            'message' => 'Call route created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CallRoute $route)
    {
        return response()->json($route);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallRoute $route)
    {
        $oldValues = $route->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'destination_type' => 'required|in:extension,queue,ivr,trunk',
            'destination_value' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:100',
            'context' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'time_conditions' => 'nullable|array',
        ]);

        $route->update($validated);

        AuditLog::log('update', $route, $request->user(), $oldValues, $validated, "Updated call route {$route->name}");

        return response()->json([
            'route' => $route,
            'message' => 'Call route updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CallRoute $route)
    {
        $oldValues = $route->toArray();

        $route->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted call route {$route->name}");

        return response()->json([
            'message' => 'Call route deleted successfully',
        ]);
    }

    /**
     * Test a call route pattern
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'pattern' => 'required|string',
            'test_number' => 'required|string',
        ]);

        // Simple pattern matching (Asterisk-style)
        $pattern = $validated['pattern'];
        $testNumber = $validated['test_number'];

        // Convert Asterisk pattern to regex
        $regex = $this->asteriskPatternToRegex($pattern);
        $matches = preg_match($regex, $testNumber);

        return response()->json([
            'pattern' => $pattern,
            'test_number' => $testNumber,
            'matches' => (bool) $matches,
            'regex' => $regex,
        ]);
    }

    /**
     * Convert Asterisk dialplan pattern to regex
     */
    private function asteriskPatternToRegex(string $pattern): string
    {
        // Asterisk pattern rules:
        // X = 0-9, Z = 1-9, N = 2-9, [123] = match 1,2,3, . = wildcard
        $regex = str_replace(
            ['X', 'Z', 'N', '.', '[', ']'],
            ['[0-9]', '[1-9]', '[2-9]', '.*', '[', ']'],
            $pattern
        );

        return '/^'.$regex.'$/';
    }
}
