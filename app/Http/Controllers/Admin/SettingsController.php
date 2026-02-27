<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
        public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
