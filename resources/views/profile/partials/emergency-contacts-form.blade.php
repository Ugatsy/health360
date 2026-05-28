<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Emergency Contacts
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            These contacts will be notified via SMS when a symptom check returns a high or emergency risk level.
        </p>
    </header>

    <div class="mt-6 space-y-4" id="contacts-wrapper">
        @forelse(auth()->user()->emergencyContacts as $contact)
            <div class="contact-row flex items-center gap-3 p-3 border rounded-lg bg-gray-50">
                <div class="flex-1">
                    <p class="font-medium text-sm">{{ $contact->name }}</p>
                    <p class="text-sm text-gray-500">{{ $contact->phone_number }} @if($contact->relationship) &middot; {{ $contact->relationship }} @endif</p>
                </div>
                <form action="{{ route('emergency-contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Remove this contact?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700 text-sm font-medium">Remove</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-400" id="no-contacts-msg">No emergency contacts added yet.</p>
        @endforelse
    </div>

    <button type="button" id="add-contact-btn" class="mt-4 inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-teal-700 bg-teal-50 border border-teal-200 rounded-lg hover:bg-teal-100">
        + Add contact
    </button>

    <div id="contact-form" class="hidden mt-4 p-4 border rounded-lg bg-white">
        <form action="{{ route('emergency-contacts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone number</label>
                <input type="tel" name="phone_number" required placeholder="+1234567890" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Relationship <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="relationship" placeholder="e.g. Spouse, Parent, Sibling" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">Save</button>
                <button type="button" id="cancel-contact-btn" class="px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('add-contact-btn');
    const form = document.getElementById('contact-form');
    const cancel = document.getElementById('cancel-contact-btn');
    if (btn && form) {
        btn.addEventListener('click', () => { form.classList.remove('hidden'); btn.classList.add('hidden'); });
        cancel.addEventListener('click', () => { form.classList.add('hidden'); btn.classList.remove('hidden'); });
    }
});
</script>
