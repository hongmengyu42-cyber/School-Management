<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Replaces the legacy admin "System Settings" screen. Deliberately not a
 * full resource controller since system_settings is a flat key/value store,
 * not a list of records to CRUD individually.
 */
class SystemSettingController extends AdminController
{
    public function edit()
    {
        return view('admin.system-settings.edit', [
            'schoolName' => Setting::schoolName(),
            'passingThreshold' => Setting::passingThreshold(),
            'consecutiveAbsenceAlertThreshold' => Setting::consecutiveAbsenceAlertThreshold(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'passing_grade_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'consecutive_absence_alert_threshold' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        Setting::set('school_name', $validated['school_name']);
        Setting::set('passing_grade_threshold', $validated['passing_grade_threshold']);
        Setting::set('consecutive_absence_alert_threshold', $validated['consecutive_absence_alert_threshold']);

        $this->logActivity($request, 'settings.updated', 'Updated system settings');

        return redirect()->route('admin.system-settings.edit')->with('status', 'Settings updated.');
    }
}
