<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPosyanduController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
    
        if (!$user->posyandu_id) {
            return redirect()->route('kader.dashboard')
                ->with('error', 'Akun Anda belum terhubung ke Posyandu.');
        }
    
        $query = JadwalPosyandu::with(['posyandu', 'kader'])
            ->where('posyandu_id', $user->posyandu_id);
    
        // SEARCH: kegiatan / lokasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
    
        // FILTER: Status
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('tanggal', '>=', now());
            } elseif ($request->status === 'past') {
                $query->where('tanggal', '<', now());
            } elseif ($request->status === 'today') {
                $query->whereDate('tanggal', today());
            }
        }
    
        $jadwals = $query->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
    
        return view('kader.jadwal.index', compact('jadwals'));
    }
    
    public function riwayat(Request $request)
    {
        $query = JadwalPosyandu::with(['posyandu', 'kader']);
    
        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
    
        if ($request->filled('rw')) {
            $posyanduIds = Posyandu::where('rw', $request->rw)->pluck('id');
            $query->whereIn('posyandu_id', $posyanduIds);
        }
    
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('tanggal', '>=', now());
            } elseif ($request->status === 'past') {
                $query->where('tanggal', '<', now());
            } elseif ($request->status === 'today') {
                $query->whereDate('tanggal', today());
            }
        }
    
        $jadwals   = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        $posyandus = Posyandu::orderBy('rw')->get();
    
        return view('jadwal.riwayat', compact('jadwals', 'posyandus'));
    }

    public function create()
    {
        return view('kader.jadwal.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->posyandu_id) {
            return back()->withErrors(['posyandu_id' => 'Akun Anda tidak terhubung ke Posyandu.'])->withInput();
        }

        $validated = $request->validate([
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'required|string|max:255',
            'kegiatan'      => 'required|string|max:255',
            'keterangan'    => 'nullable|string|max:1000',
        ]);

        // CEK DUPLIKASI (Tahap 5 - sekaligus dikerjakan)
        $duplikat = JadwalPosyandu::where('posyandu_id', $user->posyandu_id)
            ->where('tanggal', $validated['tanggal'])
            ->where('waktu_mulai', $validated['waktu_mulai'])
            ->exists();

        if ($duplikat) {
            return back()->withErrors([
                'tanggal' => 'Jadwal dengan tanggal dan waktu yang sama sudah ada untuk posyandu Anda.',
            ])->withInput();
        }

        $validated['posyandu_id'] = $user->posyandu_id;
        $validated['kader_id']    = $user->id;

        JadwalPosyandu::create($validated);

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal posyandu berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jadwal = JadwalPosyandu::findOrFail($id);
        $this->authorizeKader($jadwal);

        return view('kader.jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPosyandu::findOrFail($id);
        $this->authorizeKader($jadwal);

        $user = Auth::user();

        $validated = $request->validate([
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'required|string|max:255',
            'kegiatan'      => 'required|string|max:255',
            'keterangan'    => 'nullable|string|max:1000',
        ]);

        // CEK DUPLIKASI (kecuali dirinya sendiri)
        $duplikat = JadwalPosyandu::where('posyandu_id', $user->posyandu_id)
            ->where('tanggal', $validated['tanggal'])
            ->where('waktu_mulai', $validated['waktu_mulai'])
            ->where('id', '!=', $jadwal->id)
            ->exists();

        if ($duplikat) {
            return back()->withErrors([
                'tanggal' => 'Jadwal dengan tanggal dan waktu yang sama sudah ada.',
            ])->withInput();
        }

        $jadwal->update($validated);

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal posyandu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPosyandu::findOrFail($id);
        $this->authorizeKader($jadwal);

        $jadwal->delete();

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal posyandu berhasil dihapus!');
    }

    /**
     * Cek hak akses: hanya kader pemilik posyandu yang bisa edit/hapus
     */
    private function authorizeKader(JadwalPosyandu $jadwal): void
    {
        $user = Auth::user();

        if ($user->isKader() && $jadwal->posyandu_id === $user->posyandu_id) {
            return;
        }

        abort(403, 'Anda tidak berhak mengakses jadwal ini.');
    }
}