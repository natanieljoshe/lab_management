<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Labs as Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::all()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();

        return view('rooms.index', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:200',
            'type' => 'required|in:internal,external',
            'is_manual_only' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        Room::create([
            'location' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'manual_only' => $request->boolean('is_manual_only'),
            'is_active' => $request->boolean('is_active'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:200',
            'type' => 'required|in:internal,external',
            'is_manual_only' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $room->update([
            'location' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'manual_only' => $request->boolean('is_manual_only'),
            'is_active' => $request->boolean('is_active'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        // Check if referenced
        if ($room->classMeetings()->exists() || $room->labSessions()->exists() || $room->desks()->exists()) {
            // Soft deactivate
            $room->update(['is_active' => false]);

            return back()->with('warning', "Ruangan {$room->name} memiliki data terkait (jadwal/meja) sehingga dinonaktifkan alih-alih dihapus permanen.");
        }

        $room->delete();

        return back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
