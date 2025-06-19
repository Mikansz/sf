<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Penggajian::with('karyawan');

        if ($user->isKaryawan()) {
            $query->where('karyawan_id', $user->karyawan->id);
        }

        if ($request->filled('periode_gaji')) {
            $query->where('periode_gaji', $request->periode_gaji);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('karyawan_id') && ($user->isHRD() || $user->isCEO())) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $penggajian = $query->latest('periode_gaji')->paginate(15);
        $karyawan = $user->isKaryawan() ? null : Karyawan::where('status', 'AKTIF')->get();

        return view('penggajian.index', compact('penggajian', 'karyawan'));
    }

    public function create()
    {
        if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
            abort(403, 'Unauthorized action.');
        }

        $karyawan = Karyawan::where('status', 'AKTIF')->get();
        return view('penggajian.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'periode_gaji' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        
        $existingPenggajian = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('periode_gaji', $request->periode_gaji)
            ->first();

        if ($existingPenggajian) {
            return redirect()->back()->with('error', 'Penggajian untuk periode ini sudah ada');
        }

        $this->generatePenggajian($karyawan, $request->periode_gaji);

        return redirect()->route('penggajian.index')->with('success', 'Penggajian berhasil digenerate');
    }

    public function generateBulk(Request $request)
    {
        if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'periode_gaji' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $karyawanAktif = Karyawan::where('status', 'AKTIF')->get();
        $count = 0;

        foreach ($karyawanAktif as $karyawan) {
            $existingPenggajian = Penggajian::where('karyawan_id', $karyawan->id)
                ->where('periode_gaji', $request->periode_gaji)
                ->first();

            if (!$existingPenggajian) {
                $this->generatePenggajian($karyawan, $request->periode_gaji);
                $count++;
            }
        }

        return redirect()->route('penggajian.index')
            ->with('success', "Berhasil generate penggajian untuk {$count} karyawan");
    }

    private function generatePenggajian(Karyawan $karyawan, string $periode)
    {
        [$year, $month] = explode('-', $periode);
        
        $totalHariKerja = Carbon::create($year, $month)->daysInMonth;
        
        $absensiData = Absensi::where('karyawan_id', $karyawan->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalHadir = $absensiData->get('HADIR', 0);
        $totalAlpha = $absensiData->get('ALPHA', 0);
        $totalIzin = $absensiData->get('IZIN', 0);
        $totalSakit = $absensiData->get('SAKIT', 0);

        $totalLembur = Lembur::where('karyawan_id', $karyawan->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'DISETUJUI')
            ->sum('total_bayar_lembur');

        $totalTunjangan = $karyawan->karyawanTunjangan()
            ->where('status_aktif', true)
            ->where('tanggal_mulai', '<=', Carbon::create($year, $month)->endOfMonth())
            ->where(function ($query) use ($year, $month) {
                $query->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', Carbon::create($year, $month)->startOfMonth());
            })
            ->sum('nominal');

        $gajiPokok = $karyawan->gaji_pokok;
        $persentaseKehadiran = $totalHariKerja > 0 ? $totalHadir / $totalHariKerja : 0;
        $totalKehadiran = $gajiPokok * $persentaseKehadiran;

        Penggajian::create([
            'karyawan_id' => $karyawan->id,
            'periode_gaji' => $periode,
            'gaji_pokok' => $gajiPokok,
            'total_tunjangan' => $totalTunjangan,
            'total_lembur' => $totalLembur,
            'total_kehadiran' => $totalKehadiran,
            'total_hari_kerja' => $totalHariKerja,
            'total_hadir' => $totalHadir,
            'total_alpha' => $totalAlpha,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'status' => 'DRAFT',
        ]);
    }

    public function show(Penggajian $penggajian)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan() && $penggajian->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Unauthorized action.');
        }

        $penggajian->load('karyawan');
        return view('penggajian.show', compact('penggajian'));
    }

    public function approve(Request $request, Penggajian $penggajian)
    {
        if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
            abort(403, 'Unauthorized action.');
        }

        $penggajian->update([
            'status' => 'DISETUJUI',
        ]);

        return redirect()->route('penggajian.index')->with('success', 'Penggajian berhasil disetujui');
    }

    public function pay(Request $request, Penggajian $penggajian)
    {
        if (!Auth::user()->isHRD() && !Auth::user()->isCEO()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        $penggajian->update([
            'status' => 'DIBAYAR',
            'tanggal_bayar' => $request->tanggal_bayar,
        ]);

        return redirect()->route('penggajian.index')->with('success', 'Penggajian berhasil dibayar');
    }

    public function slipGaji(Penggajian $penggajian)
    {
        $user = Auth::user();
        
        if ($user->isKaryawan() && $penggajian->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Unauthorized action.');
        }

        $penggajian->load('karyawan');
        return view('penggajian.slip', compact('penggajian'));
    }
}
