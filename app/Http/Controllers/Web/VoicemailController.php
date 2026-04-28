<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Voicemail;
use Illuminate\Http\Request;

class VoicemailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Voicemail::with('extension');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mailbox', 'like', "%{$search}%")
                  ->orWhere('fullname', 'like', "%{$search}%");
            });
        }

        $voicemails = $query->orderBy('mailbox')->paginate(25);

        return view('voicemails.index', compact('voicemails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('voicemails.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store method
        return redirect()->route('voicemails.index')->with('success', 'Voicemail created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voicemail $voicemail)
    {
        return view('voicemails.show', compact('voicemail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voicemail $voicemail)
    {
        return view('voicemails.edit', compact('voicemail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voicemail $voicemail)
    {
        // TODO: Implement update method
        return redirect()->route('voicemails.index')->with('success', 'Voicemail updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Voicemail $voicemail)
    {
        // TODO: Implement destroy method
        return redirect()->route('voicemails.index')->with('success', 'Voicemail deleted successfully');
    }
}
