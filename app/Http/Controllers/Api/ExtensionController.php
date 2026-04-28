<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExtensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Extension::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('extension_number', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $extensions = $query->orderBy('extension_number')->paginate(25);

        return response()->json($extensions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'extension_number' => 'required|string|unique:extensions|regex:/^[0-9]+$/',
            'display_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'device_type' => 'required|in:sip,iax,pjsip',
            'context' => 'nullable|string|max:255',
            'voicemail_enabled' => 'boolean',
            'call_forwarding_enabled' => 'boolean',
            'dnd_enabled' => 'boolean',
        ]);

        // Generate secure password
        $password = Str::random(12);
        $validated['password'] = Hash::make($password);
        $validated['status'] = 'offline';

        // Set defaults
        $validated['context'] = $validated['context'] ?? 'default';
        $validated['voicemail_enabled'] = $validated['voicemail_enabled'] ?? true;

        $extension = Extension::create($validated);

        // Log the creation
        AuditLog::log('create', $extension, $request->user(), [], $validated, "Created extension {$extension->extension_number}");

        return response()->json([
            'extension' => $extension,
            'generated_password' => $password, // Only shown once for security
            'message' => 'Extension created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Extension $extension)
    {
        return response()->json($extension->load(['users', 'queues']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extension $extension)
    {
        $oldValues = $extension->toArray();

        $validated = $request->validate([
            'extension_number' => ['required', 'string', 'regex:/^[0-9]+$/', Rule::unique('extensions')->ignore($extension->id)],
            'display_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'device_type' => 'required|in:sip,iax,pjsip',
            'context' => 'nullable|string|max:255',
            'status' => 'nullable|in:online,offline,ringing,busy',
            'voicemail_enabled' => 'boolean',
            'call_forwarding_enabled' => 'boolean',
            'call_forwarding_number' => 'nullable|string',
            'dnd_enabled' => 'boolean',
            'ring_group' => 'nullable|string',
            'follow_me_numbers' => 'nullable|array',
        ]);

        $extension->update($validated);

        // Log the update
        AuditLog::log('update', $extension, $request->user(), $oldValues, $validated, "Updated extension {$extension->extension_number}");

        return response()->json([
            'extension' => $extension,
            'message' => 'Extension updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Extension $extension)
    {
        $oldValues = $extension->toArray();

        // Check if extension is in use
        if ($extension->users()->exists() || $extension->queues()->exists()) {
            return response()->json([
                'error' => 'Cannot delete extension that is assigned to users or queues'
            ], 422);
        }

        $extension->delete();

        // Log the deletion
        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted extension {$extension->extension_number}");

        return response()->json([
            'message' => 'Extension deleted successfully'
        ]);
    }
}
