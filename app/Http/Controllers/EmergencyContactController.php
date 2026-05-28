<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;

class EmergencyContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'relationship' => 'nullable|string|max:255',
        ]);

        auth()->user()->emergencyContacts()->create($validated);

        return back()->with('success', 'Emergency contact added.');
    }

    public function destroy(EmergencyContact $contact)
    {
        if ($contact->user_id !== auth()->id()) {
            abort(403);
        }

        $contact->delete();

        return back()->with('success', 'Emergency contact removed.');
    }
}
