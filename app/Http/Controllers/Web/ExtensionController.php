<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

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

        return view('extensions.index', compact('extensions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('extensions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'extension_number' => 'required|string|unique:extensions|regex:/^[0-9]+$/',
            'display_name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email',
            'device_type' => 'required|in:sip,pjsip',
            'context' => 'required|string|max:255',
            'call_forwarding_enabled' => 'boolean',
            'call_forwarding_number' => 'nullable|string|max:255',
            'dnd_enabled' => 'boolean',
            'voicemail_enabled' => 'boolean',
            'voicemail_box' => 'nullable|string|max:255',
        ]);

        try {
            Extension::create($validated);

            return redirect()->route('extensions.index')->with('success', 'Extension created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create extension: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Extension $extension)
    {
        return view('extensions.show', compact('extension'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Extension $extension)
    {
        return view('extensions.edit', compact('extension'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extension $extension)
    {
        $validated = $request->validate([
            'extension_number' => 'required|string|regex:/^[0-9]+$/|unique:extensions,extension_number,'.$extension->id,
            'display_name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email',
            'device_type' => 'required|in:sip,pjsip',
            'context' => 'required|string|max:255',
            'call_forwarding_enabled' => 'boolean',
            'call_forwarding_number' => 'nullable|string|max:255',
            'dnd_enabled' => 'boolean',
            'voicemail_enabled' => 'boolean',
            'voicemail_box' => 'nullable|string|max:255',
        ]);

        try {
            $extension->update($validated);

            return redirect()->route('extensions.index')->with('success', 'Extension updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update extension: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Extension $extension)
    {
        try {
            $extension->delete();

            return redirect()->route('extensions.index')->with('success', 'Extension deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete extension');
        }
    }
}
