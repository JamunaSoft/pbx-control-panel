<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voicemail;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VoicemailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Voicemail::with(['extension']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mailbox', 'like', "%{$search}%")
                  ->orWhere('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $voicemails = $query->orderBy('mailbox')->paginate(25);

        return response()->json($voicemails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mailbox' => 'required|string|unique:voicemails|max:255',
            'context' => 'nullable|string|max:255',
            'password' => 'required|string|min:4|max:10',
            'fullname' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'pager' => 'nullable|string|max:255',
            'email_notification' => 'boolean',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'delete_after_email' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $validated['context'] = $validated['context'] ?? 'default';
        $validated['language'] = $validated['language'] ?? 'en';
        $validated['timezone'] = $validated['timezone'] ?? 'UTC';
        $validated['email_notification'] = $validated['email_notification'] ?? true;
        $validated['enabled'] = $validated['enabled'] ?? true;

        $voicemail = Voicemail::create($validated);

        AuditLog::log('create', $voicemail, $request->user(), [], $validated, "Created voicemail box {$voicemail->mailbox}");

        return response()->json([
            'voicemail' => $voicemail,
            'message' => 'Voicemail box created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Voicemail $voicemail)
    {
        return response()->json($voicemail->load(['extension']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voicemail $voicemail)
    {
        $oldValues = $voicemail->toArray();

        $validated = $request->validate([
            'mailbox' => ['required', 'string', 'max:255', Rule::unique('voicemails')->ignore($voicemail->id)],
            'context' => 'nullable|string|max:255',
            'password' => 'required|string|min:4|max:10',
            'fullname' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'pager' => 'nullable|string|max:255',
            'email_notification' => 'boolean',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'delete_after_email' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $voicemail->update($validated);

        AuditLog::log('update', $voicemail, $request->user(), $oldValues, $validated, "Updated voicemail box {$voicemail->mailbox}");

        return response()->json([
            'voicemail' => $voicemail,
            'message' => 'Voicemail box updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Voicemail $voicemail)
    {
        $oldValues = $voicemail->toArray();

        // Check if voicemail is linked to an extension
        if ($voicemail->extension) {
            return response()->json([
                'error' => 'Cannot delete voicemail box that is linked to an extension'
            ], 422);
        }

        $voicemail->delete();

        AuditLog::log('delete', null, $request->user(), $oldValues, [], "Deleted voicemail box {$voicemail->mailbox}");

        return response()->json([
            'message' => 'Voicemail box deleted successfully'
        ]);
    }

    /**
     * Get voicemail messages (would integrate with Asterisk voicemail storage)
     */
    public function messages(Request $request, Voicemail $voicemail)
    {
        // This would normally query Asterisk voicemail files
        // For now, return empty array
        return response()->json([
            'voicemail' => $voicemail,
            'messages' => [],
            'message' => 'Voicemail messages require Asterisk integration'
        ]);
    }

    /**
     * Delete voicemail message
     */
    public function deleteMessage(Request $request, Voicemail $voicemail)
    {
        $validated = $request->validate([
            'message_id' => 'required|string',
        ]);

        // This would normally delete the voicemail file
        AuditLog::log('delete', $voicemail, $request->user(), [], [],
            "Attempted to delete voicemail message {$validated['message_id']} from mailbox {$voicemail->mailbox}");

        return response()->json([
            'message' => 'Voicemail deletion requires Asterisk integration',
            'message_id' => $validated['message_id']
        ]);
    }
}
