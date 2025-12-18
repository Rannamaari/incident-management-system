<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts with search and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $query = Contact::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->category($request->category);
        }

        // Filter by atoll
        if ($request->has('atoll') && $request->atoll) {
            $query->atoll($request->atoll);
        }

        // Get contacts with pagination
        $contacts = $query->orderBy('name', 'asc')->paginate($perPage);

        // Get unique categories and atolls for filters
        $categories = Contact::distinct()->pluck('category')->filter()->sort()->values();
        $atolls = Contact::distinct()->pluck('atoll')->filter()->sort()->values();

        return view('contacts.index', compact('contacts', 'categories', 'atolls'));
    }

    /**
     * Display the specified contact
     */
    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'atoll' => ['nullable', 'string', 'max:255'],
            'island' => ['nullable', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Contact::create($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'atoll' => ['nullable', 'string', 'max:255'],
            'island' => ['nullable', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    /**
     * Remove multiple contacts from storage.
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'contact_ids' => ['required', 'array'],
            'contact_ids.*' => ['required', 'integer', 'exists:contacts,id'],
        ]);

        $count = Contact::whereIn('id', $validated['contact_ids'])->delete();

        return redirect()->route('contacts.index')
            ->with('success', "Successfully deleted {$count} contact(s).");
    }
}
