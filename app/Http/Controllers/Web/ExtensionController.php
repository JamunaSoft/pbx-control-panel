<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExtensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $response = Http::get(route('api.extensions.index', $request->query()));
            $extensions = $response->successful() ? $response->json()['data'] ?? [] : [];
        } catch (\Exception $e) {
            $extensions = [];
        }

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
        try {
            $response = Http::post(route('api.extensions.store'), $request->all());

            if ($response->successful()) {
                return redirect()->route('extensions.index')->with('success', 'Extension created successfully');
            }

            return back()->withErrors($response->json())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create extension')->withInput();
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
        try {
            $response = Http::put(route('api.extensions.update', $extension), $request->all());

            if ($response->successful()) {
                return redirect()->route('extensions.index')->with('success', 'Extension updated successfully');
            }

            return back()->withErrors($response->json())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update extension')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Extension $extension)
    {
        try {
            $response = Http::delete(route('api.extensions.destroy', $extension));

            if ($response->successful()) {
                return redirect()->route('extensions.index')->with('success', 'Extension deleted successfully');
            }

            return back()->with('error', 'Failed to delete extension');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete extension');
        }
    }
}
