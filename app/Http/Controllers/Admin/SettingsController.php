<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends BaseController
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', ['settings' => $settings]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        if ($request->ajax()) {
            return $this->jsonSuccess('Settings updated successfully');
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}

