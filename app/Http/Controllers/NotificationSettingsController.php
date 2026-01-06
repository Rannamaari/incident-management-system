<?php

namespace App\Http\Controllers;

use App\Models\NotificationLevel;
use App\Models\NotificationRecipient;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationSettingsController extends Controller
{
    /**
     * Display notification settings page
     */
    public function index()
    {
        $levels = NotificationLevel::with('recipients')
            ->ordered()
            ->get();

        return view('notification-settings.index', compact('levels'));
    }

    /**
     * Store a new notification level
     */
    public function storeLevel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'severities' => 'required|array|min:1',
            'severities.*' => 'required|string|in:Low,Medium,High',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        NotificationLevel::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'severities' => $validated['severities'],
            'is_active' => true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('notification-settings.index')
            ->with('success', 'Notification level created successfully.');
    }

    /**
     * Update a notification level
     */
    public function updateLevel(Request $request, NotificationLevel $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'severities' => 'required|array|min:1',
            'severities.*' => 'required|string|in:Low,Medium,High',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $level->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'severities' => $validated['severities'],
            'is_active' => $validated['is_active'] ?? $level->is_active,
            'sort_order' => $validated['sort_order'] ?? $level->sort_order,
        ]);

        return redirect()->route('notification-settings.index')
            ->with('success', 'Notification level updated successfully.');
    }

    /**
     * Delete a notification level
     */
    public function destroyLevel(NotificationLevel $level)
    {
        $level->delete();

        return redirect()->route('notification-settings.index')
            ->with('success', 'Notification level deleted successfully.');
    }

    /**
     * Toggle notification level active status
     */
    public function toggleLevel(NotificationLevel $level)
    {
        $level->update([
            'is_active' => !$level->is_active,
        ]);

        return back()->with('success', 'Notification level status updated successfully.');
    }

    /**
     * Store a new recipient for a notification level
     */
    public function storeRecipient(Request $request, NotificationLevel $level)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('notification_recipients')->where(function ($query) use ($level) {
                    return $query->where('notification_level_id', $level->id);
                }),
            ],
            'name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $level->recipients()->create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'department' => $validated['department'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Recipient added successfully.');
    }

    /**
     * Update a recipient
     */
    public function updateRecipient(Request $request, NotificationRecipient $recipient)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('notification_recipients')->where(function ($query) use ($recipient) {
                    return $query->where('notification_level_id', $recipient->notification_level_id);
                })->ignore($recipient->id),
            ],
            'name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $recipient->update([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? $recipient->name,
            'department' => $validated['department'] ?? $recipient->department,
        ]);

        return back()->with('success', 'Recipient updated successfully.');
    }

    /**
     * Delete a recipient
     */
    public function destroyRecipient(NotificationRecipient $recipient)
    {
        $recipient->delete();

        return back()->with('success', 'Recipient removed successfully.');
    }

    /**
     * Toggle recipient active status
     */
    public function toggleRecipient(NotificationRecipient $recipient)
    {
        $recipient->update([
            'is_active' => !$recipient->is_active,
        ]);

        return back()->with('success', 'Recipient status updated successfully.');
    }

    /**
     * Update auto-send setting
     */
    public function updateAutoSendSetting(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        NotificationSetting::set('auto_send_enabled', $validated['enabled']);

        $status = $validated['enabled'] ? 'enabled' : 'disabled';

        return back()->with('success', "Auto-send notifications {$status} successfully.");
    }
}
