<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Trunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $response = Http::get(route('api.trunks.index', $request->query()));
            $trunks = $response->successful() ? $response->json()['data'] ?? [] : [];
        } catch (\Exception $e) {
            $trunks = [];
        }

        return view('trunks.index', compact('trunks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trunks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $response = Http::post(route('api.trunks.store'), $request->all());

            if ($response->successful()) {
                return redirect()->route('trunks.index')->with('success', 'Trunk created successfully');
            }

            return back()->withErrors($response->json())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create trunk')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trunk $trunk)
    {
        return view('trunks.show', compact('trunk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trunk $trunk)
    {
        return view('trunks.edit', compact('trunk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trunk $trunk)
    {
        try {
            $response = Http::put(route('api.trunks.update', $trunk), $request->all());

            if ($response->successful()) {
                return redirect()->route('trunks.index')->with('success', 'Trunk updated successfully');
            }

            return back()->withErrors($response->json())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update trunk')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Trunk $trunk)
    {
        try {
            $response = Http::delete(route('api.trunks.destroy', $trunk));

            if ($response->successful()) {
                return redirect()->route('trunks.index')->with('success', 'Trunk deleted successfully');
            }

            return back()->with('error', 'Failed to delete trunk');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete trunk');
        }
    }
}
