<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $timezones = [
            'Asia/Jakarta' => 'WIB (Jakarta, Bandung, Surabaya)',
            'Asia/Makassar' => 'WITA (Makassar, Denpasar, Balikpapan)',
            'Asia/Jayapura' => 'WIT (Jayapura, Manokwari)',
            'UTC' => 'UTC (Coordinated Universal Time)'
        ];

        $currentTimezone = Setting::get('app_timezone', 'Asia/Jakarta');
        
        return view('admin.settings.index', compact('timezones', 'currentTimezone'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_timezone' => 'required|string'
        ]);

        Setting::set('app_timezone', $request->app_timezone);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}