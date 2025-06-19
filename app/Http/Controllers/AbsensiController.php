<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Absensi::with('karyawan');

        if ($user->isKaryawan()) {
            $query->where('karyawan_id', $user->karyawan->id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('karyawan_id') && ($user->isHRD() || $user->isCEO())) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $absensi = $query->latest('tanggal')->paginate(15);
        $karyawan = $user->isKaryawan() ? null : Karyawan::where('status', 'AKTIF')->get();

        return view('absensi.index', compact('absensi', 'karyawan'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->isKaryawan()) {
            $karyawan = $user->karyawan;
            $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', Carbon::today())
                ->first();

            if ($absensiHariIni) {
                return redirect()->route('absensi.index')
                    ->with('error', 'Anda sudah melakukan absensi hari ini');
            }

            return view('absensi.create', compact('karyawan'));
        }

        $karyawan = Karyawan::where('status', 'AKTIF')->get();
        return view('absensi.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'tanggal' => 'required|date',
            'status' => 'required|in:HADIR,IZIN,SAKIT,ALPHA,CUTI',
            'keterangan' => 'nullable|string',
        ];

        if ($user->isHRD() || $user->isCEO()) {
            $rules['karyawan_id'] = 'required|exists:karyawan,id';
        }

        if ($request->status === 'HADIR') {
            $rules['jam_masuk'] = 'required';
            $rules['lokasi_masuk'] = 'required|string|max:50';
        }

        $request->validate($rules);

        $data = [
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ];

        if ($user->isKaryawan()) {
            $data['karyawan_id'] = $user->karyawan->id;
        } else {
            $data['karyawan_id'] = $request->karyawan_id;
        }

        if ($request->status === 'HADIR') {
            $data['jam_masuk'] = $request->jam_masuk;
            $data['lokasi_masuk'] = $request->lokasi_masuk;
        }

        $absensi = Absensi::create($data);

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil dicatat');
    }

    public function show(Absensi $absensi)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan() && $absensi->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Unauthorized action.');
        }

        $absensi->load('karyawan');
        return view('absensi.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan()) {
            if ($absensi->karyawan_id !== $user->karyawan->id) {
                abort(403, 'Unauthorized action.');
            }
            
            if ($absensi->tanggal->lt(Carbon::today())) {
                return redirect()->route('absensi.index')
                    ->with('error', 'Tidak dapat mengubah absensi hari sebelumnya');
            }
        }

        return view('absensi.edit', compact('absensi'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan() && $absensi->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'status' => 'required|in:HADIR,IZIN,SAKIT,ALPHA,CUTI',
            'keterangan' => 'nullable|string',
        ];

        if ($request->status === 'HADIR') {
            if (!$absensi->jam_masuk && !$request->jam_masuk) {
                $rules['jam_masuk'] = 'required';
                $rules['lokasi_masuk'] = 'required|string|max:50';
            }
            
            if (!$absensi->jam_keluar && $request->jam_keluar) {
                $rules['jam_keluar'] = 'required|after:jam_masuk';
                $rules['lokasi_keluar'] = 'required|string|max:50';
            }
        }

        $request->validate($rules);

        $data = [
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ];

        if ($request->status === 'HADIR') {
            if ($request->jam_masuk) {
                $data['jam_masuk'] = $request->jam_masuk;
                $data['lokasi_masuk'] = $request->lokasi_masuk;
            }
            
            if ($request->jam_keluar) {
                $data['jam_keluar'] = $request->jam_keluar;
                $data['lokasi_keluar'] = $request->lokasi_keluar;
            }
        }

        $absensi->update($data);
        $absensi->hitungJamKerja();
        $absensi->save();

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui');
    }

    public function clockOut(Request $request, Absensi $absensi)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan() && $absensi->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'jam_keluar' => 'required',
            'lokasi_keluar' => 'required|string|max:50',
        ]);

        $absensi->update([
            'jam_keluar' => $request->jam_keluar,
            'lokasi_keluar' => $request->lokasi_keluar,
        ]);

        $absensi->hitungJamKerja();
        $absensi->save();

        return redirect()->route('absensi.index')->with('success', 'Clock out berhasil dicatat');
    }
}
